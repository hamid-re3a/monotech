<?php

namespace User\Http\Controllers\Front;


use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use User\Http\Requests\User\profile\ChangePasswordRequest;
use User\Http\Requests\User\profile\UpdatePersonalDetails;
use User\Http\Resources\User\ProfileDetailsResource;
use User\Jobs\EmailJob;
use User\Mail\User\PasswordChangedEmail;
use User\Support\UserActivityHelper;

class UserController extends Controller
{


    /**
     * Get user profile details
     * @group Public User > Profile Management
     */
    public function getDetails()
    {
        return api()->success(null, ProfileDetailsResource::make(auth()->user()));
    }

    /**
     * Change password
     * @group Public User > Profile Management
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {

        try {
            DB::beginTransaction();
            $request->user()->update([
                'password' => $request->get('password') //bcrypt in User model (Mutator)
            ]);

            list($ip_db, $agent_db) = UserActivityHelper::getInfo($request);
            if (getSetting('IS_LOGIN_PASSWORD_CHANGE_EMAIL_ENABLE'))
                EmailJob::dispatch(new PasswordChangedEmail($request->user(), $ip_db, $agent_db), $request->user()->email);

            DB::commit();
            return api()->success(trans('user.responses.password-successfully-changed'));
        } catch (\Throwable $exception) {
            DB::rollBack();
            return api()->error(trans('user.responses.global-error'));
        }
    }


    /**
     * Change personal details
     * @group Public User > Profile Management
     * @param UpdatePersonalDetails $request
     * @return JsonResponse
     */
    public function updatePersonalDetails(UpdatePersonalDetails $request)
    {
        try {
            DB::beginTransaction();
            $request->user()->update($request->validated());
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            return api()->error(trans('user.responses.global-error'), null, 500, null);
        }

        return api()->success(trans('user.responses.profile-details-updated'), ProfileDetailsResource::make(auth()->user()));
    }


}
