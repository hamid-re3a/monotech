<?php

namespace User\Http\Controllers\Front;


use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use User\Http\Requests\Auth\EmailExistenceRequest;
use User\Http\Requests\Auth\EmailVerificationOtpRequest;
use User\Http\Requests\Auth\ForgetPasswordRequest;
use User\Http\Requests\Auth\LoginRequest;
use User\Http\Requests\Auth\RegisterUserRequest;
use User\Http\Requests\Auth\ResetForgetPasswordRequest;
use User\Http\Requests\Auth\UsernameExistenceRequest;
use User\Http\Requests\Auth\VerifyEmailOtpRequest;
use User\Http\Resources\Auth\ProfileResource;
use User\Jobs\EmailJob;
use User\Mail\User\NormalLoginEmail;
use User\Mail\User\PasswordChangedEmail;
use User\Mail\User\SuccessfulEmailVerificationEmail;
use User\Models\LoginAttempt;
use User\Models\User;
use MLM\Services\MlmClientFacade;
use User\Support\UserActivityHelper;

class AuthController extends Controller
{

    /**
     * Register New User
     * @group
     * Auth
     * @unauthenticated
     * @param RegisterUserRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function register(RegisterUserRequest $request)
    {

        //Check if system is not available
        if ($this->systemIsUnderMaintenance())
            return api()->error(null, [
                'subject' => trans('user.responses.we-are-under-maintenance')
            ], 406);

        $data = $request->validated();
        $user = User::query()->create($data);
        $user->assignRole(USER_ROLE_CLIENT);

        UserActivityHelper::makeEmailVerificationOtp($user, $request);

        return api()->success(trans('user.responses.successfully-registered-go-activate-your-email'));
    }

    /**
     * Login
     * @group
     * Auth
     * @unauthenticated
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $user = User::whereEmail($credentials['email'])->first();
        if (!$user)
            $user = User::whereUsername($credentials['email'])->first();
        if (!$user)
            abort(422, trans('user.responses.invalid-inputs-from-user'));
        //Check if system is not available
        if ($user->roles()->count() == 1 AND $user->hasRole(USER_ROLE_CLIENT) AND $this->systemIsUnderMaintenance())
            return api()->error(null, [
                'subject' => trans('user.responses.we-are-under-maintenance')
            ], 406);

        $login_attempt = LoginAttempt::find($request->attributes->get('login_attempt'));

        $data = [];
        $data['left_attempts'] = $request->get('left_attempts');
        if ($login_attempt && $login_attempt->login_status == LOGIN_ATTEMPT_STATUS_BLOCKED) {
            $data['try_in'] = $request->get('try_in');
            $data['try_in_timestamp'] = $request->get('try_in_timestamp');
            return api()->error(trans('user.responses.max-attempts-exceeded'), $data, 429);
        }
        if (!Hash::check($credentials['password'], $user->password)) {
            $login_attempt->login_status = LOGIN_ATTEMPT_STATUS_FAILED;
            $login_attempt->save();

            return api()->error(trans('user.responses.invalid-inputs-from-user'), $data, 400);
        }


        if (!$user->isEmailVerified())
            return api()->error(trans('user.responses.go-activate-your-email'), null, 403);

        if ($user->isDeactivate())
            return api()->error(trans('user.responses.your-account-is-deactivate'), null, 403);

        $token = $this->getNewToken($user);

        $login_attempt->login_status = LOGIN_ATTEMPT_STATUS_SUCCESS;
        $login_attempt->save();

        EmailJob::dispatch(new NormalLoginEmail($user, $login_attempt), $user->email);
        return $this->respondWithToken($token);
    }

    /**
     * Get Current User
     * @group
     * Auth
     */
    public function getAuthUser()
    {
        return api()->success(trans('user.responses.success'), ProfileResource::make(auth()->user()));
    }

    /**
     * Check email existence
     * @group
     * Auth
     * @unauthenticated
     * @param EmailExistenceRequest $request
     * @return JsonResponse
     */
    public function isEmailExists(EmailExistenceRequest $request)
    {
        if (User::whereEmail($request->get('email'))->exists())
            return api()->success(trans('user.responses.email-already-exists'), true);

        return api()->success(trans('user.responses.email-does-not-exist'), false);
    }

    /**
     * Username existence
     * @group
     * Auth
     * @unauthenticated
     * @param UsernameExistenceRequest $request
     * @return JsonResponse
     */
    public function isUsernameExists(UsernameExistenceRequest $request)
    {
        if (User::whereUsername($request->get('username'))->exists())
            return api()->success(trans('user.responses.username-already-exists'), true);

        return api()->success(trans('user.responses.username-does-not-exist'), false);
    }

    /**
     * Ask Email Verification Otp
     * @group
     * Auth
     * @unauthenticated
     * @param EmailVerificationOtpRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function askForEmailVerificationOtp(EmailVerificationOtpRequest $request)
    {
        $user = User::whereEmail($request->get('email'))->first();

        if ($user->isDeactivate())
            return api()->error(trans('user.responses.your-account-is-deactivate'), null, 403);

        if ($user->isEmailVerified())
            return api()->success(trans('user.responses.email-is-already-verified'));

        list($data, $err) = UserActivityHelper::makeEmailVerificationOtp($user, $request, false);
        if ($err) {
            return api()->error(trans('user.responses.wait-limit'), $data, 429);
        }
        return api()->success(trans('user.responses.otp-successfully-sent'));
    }


    /**
     * Activate Email
     * @group
     * Auth
     * @unauthenticated
     * @param VerifyEmailOtpRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function verifyEmailOtp(VerifyEmailOtpRequest $request)
    {
        $user = User::whereEmail($request->get('email'))->first();
        if ($user->isEmailVerified())
            return api()->success(trans('user.responses.email-is-already-verified'));

        if ($user->isDeactivate())
            return api()->error(trans('user.responses.your-account-is-deactivate'), null, 403);

        $duration = getSetting('USER_EMAIL_VERIFICATION_OTP_DURATION');
        $otp_db = $user->otps()
            ->where('type', OTP_TYPE_EMAIL_VERIFICATION)
            ->whereBetween('created_at', [now()->subSeconds($duration)->format('Y-m-d H:i:s'), now()->format('Y-m-d H:i:s')])
            ->get()
            ->last();
        if (is_null($otp_db)) {
            $errors = [
                'otp' => trans('user.responses.email-verification-code-is-expired')
            ];
            return api()->error('user.responses.email-verification-code-is-expired', '', 422, $errors);
        }

        if ($otp_db->is_used) {
            $errors = [
                'otp' => trans('user.responses.email-verification-code-is-used')
            ];
            return api()->error('user.responses.email-verification-code-is-used', '', 422, $errors);
        }


        if ($otp_db->otp == $request->get('otp')) {
            $user->email_verified_at = now();
            $user->save();
            $otp_db->is_used = true;
            $otp_db->save();

            $token = $this->getNewToken($user);

            list($ip_db, $agent_db) = UserActivityHelper::getInfo($request);
            EmailJob::dispatch(new SuccessfulEmailVerificationEmail($user, $ip_db, $agent_db), $user->email);

            return $this->respondWithToken($token, 'user.responses.email-verified-successfully');
        }
        return api()->error('user.responses.email-verification-code-is-incorrect');

    }

    /**
     * Forget Password
     * @group
     * Auth
     * @unauthenticated
     * @param ForgetPasswordRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function forgotPassword(ForgetPasswordRequest $request)
    {
        $user = User::whereEmail($request->get('email'))->first();
        list($data, $err) = UserActivityHelper::makeForgetPasswordOtp($user, $request);
        if ($err) {
            return api()->error(trans('user.responses.wait-limit'), $data, 429);
        }
        return api()->success(trans('user.responses.otp-successfully-sent'));
    }


    /**
     * Reset Forget Password
     * @group
     * Auth
     * @unauthenticated
     * @param ResetForgetPasswordRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function resetForgetPassword(ResetForgetPasswordRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = User::whereEmail($request->get('email'))->first();
            $duration = getSetting('USER_FORGOT_PASSWORD_OTP_DURATION');
            $fp_db = $user->otps()
                ->where('type', OTP_TYPE_EMAIL_FORGOT_PASSWORD)
                ->whereBetween('created_at', [now()->subSeconds($duration)->format('Y-m-d H:i:s'), now()->format('Y-m-d H:i:s')])
                ->get()
                ->last();
            if (is_null($fp_db)) {
                $errors = [
                    'otp' => trans('user.responses.password-reset-code-is-expired')
                ];
                return api()->error(trans('user.responses.password-reset-code-is-expired'), '', 422, $errors);
            }

            if ($fp_db->is_used) {
                $errors = [
                    'otp' => trans('user.responses.password-reset-code-is-used')
                ];
                return api()->error(trans('user.responses.password-reset-code-is-used'), '', 422, $errors);
            }


            if ($fp_db->otp == $request->get('otp')) {
                $user->update([
                    'password' => $request->get('password')
                ]);

                list($ip_db, $agent_db) = UserActivityHelper::getInfo($request);
                EmailJob::dispatch(new PasswordChangedEmail($user, $ip_db, $agent_db), $user->email);

                $fp_db->update([
                    'is_used' => true
                ]);
                DB::commit();
                return api()->success(trans('user.responses.password-successfully-changed'));
            }

        } catch (Exception $exception) {
            DB::rollBack();
            return api()->error('user.responses.global-error', null, 500);
        }
        $errors = [
            'otp' => trans('user.responses.password-reset-code-is-invalid')
        ];
        return api()->error(trans('user.responses.password-reset-code-is-invalid'), null, 422, $errors);

    }

    /**
     * Log out
     * @group
     * Auth
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return api()->success(trans('user.responses.logout-successful'));
    }

    /**
     * Ping
     * @group
     * Auth
     */
    public function ping()
    {
        return api()->success(trans('user.responses.ok'));
    }


    protected function respondWithToken($token, $message = 'user.responses.login-successful')
    {
        $data = [
            'access_token' => $token->plainTextToken,
            'token_type' => 'bearer',
        ];
        return api()->success(trans($message), $data);
    }

    /**
     * @param $user
     * @return mixed
     * @throws Exception
     */
    private function getNewToken($user)
    {
        return $user->createToken(getSetting("APP_NAME"), []);
    }

    private function systemIsUnderMaintenance()
    {

        $registration_block_from_date = getSetting('SYSTEM_IS_UNDER_MAINTENANCE_FROM_DATE');
        $registration_block_from_date = strtotime($registration_block_from_date) ? Carbon::parse($registration_block_from_date) : null;

        $registration_block_to_date = getSetting('SYSTEM_IS_UNDER_MAINTENANCE_TO_DATE');
        $registration_block_to_date = strtotime($registration_block_to_date) ? Carbon::parse($registration_block_to_date) : null;

        $response = false;

        if (!empty($registration_block_from_date) OR !empty($registration_block_to_date)) {

            if (!empty($registration_block_from_date) AND !empty($registration_block_to_date))
                $response = Carbon::now()->between($registration_block_from_date, $registration_block_to_date);

            if (!empty($registration_block_from_date) AND empty($registration_block_to_date) AND $registration_block_from_date->isPast())
                $response = true;

            if (empty($registration_block_from_date) AND !empty($registration_block_to_date) AND !$registration_block_to_date->isPast())
                $response = true;
        }

//        if($response AND getSetting('LOGOUT_CLIENTS_FOR_MAINTENANCE')) //Check if we should revoke current active tokens
//            PersonalAccessToken::query()->where('tokenable_type','=','User\Models\User')->can(USER_ROLE_CLIENT)->delete();

        return $response;
    }


}
