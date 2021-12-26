<?php

namespace User\Http\Middlewares;

use User\Jobs\EmailJob;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use User\Mail\User\SuspiciousLoginAttemptEmail;
use User\Mail\User\TooManyLoginAttemptPermanentBlockedEmail;
use User\Mail\User\TooManyLoginAttemptTemporaryBlockedEmail;
use User\Mail\User\UserAccountAutomaticActivatedEmail;
use User\Models\LoginAttempt as LoginAttemptModel;
use User\Models\User;
use User\Support\UserActivityHelper;

class LoginAttemptMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $user = User::whereEmail($request->get('email'))->first();
        if (!$user)
            $user = User::whereUsername($request->get('email'))->first();

        if (!$user)
            abort(422, trans('user.responses.invalid-inputs-from-user'));

        list($ip_db, $agent_db) = UserActivityHelper::getInfo($request);

        $login_attempt = LoginAttemptModel::query()->create([
            "user_id" => is_null($user) ? null : $user->id,
            "ip_id" => is_null($ip_db) ? null : $ip_db->id,
            "agent_id" => is_null($agent_db) ? null : $agent_db->id,
        ]);

        $this->blockTooManyLoginAttempts($user, $login_attempt, $request);

        $this->sendMailForSuspiciousNewIpOrDevice($user, $ip_db, $agent_db, $login_attempt);

        $request->attributes->add(['login_attempt' => $login_attempt->id]);

        return $next($request);

    }

    /**
     * @param $ip_db
     * @param $user
     * @return bool
     */
    private function isLoginFromNewIp($ip_db, $user)
    {
        return !is_null($ip_db) && LoginAttemptModel::query()->where('user_id', $user->id)->where("ip_id", $ip_db->id)->count() == 1;
    }

    /**
     * @param $agent_db
     * @param $user
     * @return bool
     */
    private function isLoginFromNewAgent($agent_db, $user): bool
    {
        return (!is_null($agent_db) && LoginAttemptModel::query()->where('user_id', $user->id)->where("agent_id", $agent_db->id)->count() == 1);
    }

    /**
     * @param $user
     * @return bool
     */
    private function userHasAlreadyAtLeastOneLoginAttempt($user): bool
    {
        return $user->loginAttempts->count() > 1;
    }

    /**
     * @param $user
     * @param $login_attempt
     * @param $request
     * @throws \Exception
     */
    private function blockTooManyLoginAttempts($user, $login_attempt, $request): void
    {
        list($intervals, $tries) = getLoginAttemptSetting();
        $type_block = 'free';
        $blocked_tier = 0;


        $blocked_layer = LoginAttemptModel::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [now()->subDays(1)->format('Y-m-d H:i:s'), now()->format('Y-m-d H:i:s')])
            ->get()
            ->max('blocked_tier');
        $layer = 0;

        $last_failed_login = LoginAttemptModel::query()->where('login_status', [LOGIN_ATTEMPT_STATUS_FAILED])
            ->where('user_id', $user->id)->latest()->get()->first();
        if (!is_null($last_failed_login)) {

            if (!is_null($blocked_layer))
                $layer = $blocked_layer + 1;
            $try_in = Carbon::make($last_failed_login->created_at)->addSeconds($intervals[$layer])->diffForHumans();

            $try_in_sec = Carbon::make($last_failed_login->created_at)->addSeconds($intervals[$layer])->timestamp;
            $request->attributes->add(['try_in' => $try_in]);
            $request->attributes->add(['try_in_timestamp' => $try_in_sec]);
            $last_login = LoginAttemptModel::query()->where('user_id', $user->id)->latest()->take(2)->get()->last();
        }


        $failed_login_attempt_count = LoginAttemptModel::query()->whereIn('login_status', [LOGIN_ATTEMPT_STATUS_FAILED])
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [now()->subSeconds($intervals[$layer])->format('Y-m-d H:i:s'), now()->format('Y-m-d H:i:s')])
            ->count();
        if (!is_null($blocked_layer)) {
            $layer = $blocked_layer + 1;
            $already_blocked = LoginAttemptModel::query()
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [now()->subSeconds($intervals[$blocked_layer])->format('Y-m-d H:i:s'), now()->format('Y-m-d H:i:s')])
                ->where('blocked_tier', '=', $blocked_layer)
                ->exists();
            if ($already_blocked) {
//                $login_attempt->blocked_tier = $blocked_layer ;
                $login_attempt->login_status = LOGIN_ATTEMPT_STATUS_BLOCKED;
                $login_attempt->save();
                return;
            }
        }


        $request->attributes->add(['left_attempts' => $tries[$layer] - $failed_login_attempt_count]);


        if ($failed_login_attempt_count >= $tries[$layer]) {
            if ($layer == count($intervals) - 1) {
                $type_block = 'always';
                $blocked_tier = $layer;
            } else {
                $type_block = 'temp';
                $blocked_tier = $layer;
            }
        }

        if ($type_block != 'free') {
            $login_attempt->blocked_tier = $blocked_tier;
            $login_attempt->login_status = LOGIN_ATTEMPT_STATUS_BLOCKED;
            $login_attempt->save();
        }

        if ($type_block == 'temp') {
            if (isset($last_login) && isset($try_in)) {
                EmailJob::dispatch(new TooManyLoginAttemptTemporaryBlockedEmail($user, $login_attempt, $failed_login_attempt_count, $try_in), $user->email);
                EmailJob::dispatch(new UserAccountAutomaticActivatedEmail($user), $user->email)->delay($intervals[$layer]);
            }
        } else if ($type_block == 'always') {
            $user->block_type = USER_BLOCK_TYPE_AUTOMATIC;
            $user->block_reason = 'user.responses.max-login-attempt-blocked';
            $user->save();
            EmailJob::dispatch(new TooManyLoginAttemptPermanentBlockedEmail($user, $login_attempt), $user->email);
        }


    }

    /**
     * @param $user
     * @param $ip_db
     * @param $agent_db
     * @param $login_attempt
     */
    private function sendMailForSuspiciousNewIpOrDevice($user, $ip_db, $agent_db, LoginAttemptModel $login_attempt): void
    {
        if (
            $this->userHasAlreadyAtLeastOneLoginAttempt($user) &&
            (
                $this->isLoginFromNewIp($ip_db, $user) ||
                $this->isLoginFromNewAgent($agent_db, $user)
            )
        ) {
            $login_attempt->is_from_new_device = 1;
            $login_attempt->save();
            EmailJob::dispatch(new SuspiciousLoginAttemptEmail($user, $login_attempt), $user->email);
        }
    }


}
