<?php

namespace User\Http\Controllers\Admin;


use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use User\Http\Requests\Admin\ActivateOrDeactivateUserAccount;
use User\Http\Requests\Admin\BlockOrUnblockUser;
use User\Http\Requests\Admin\CreateAdminRequest;
use User\Http\Requests\Admin\FreezeOrUnfreezeUserAccountRequest;
use User\Http\Requests\Admin\GetUserDataRequest;
use User\Http\Requests\Admin\HistoryRequest;
use User\Http\Requests\Admin\MemberIdRequest;
use User\Http\Requests\Admin\UpdateUserAvatarRequest;
use User\Http\Requests\Admin\UserListRequest;
use User\Http\Requests\Admin\UserUpdateRequest;
use User\Http\Requests\Admin\VerifyUserEmailRequest;
use User\Http\Resources\OtpResource;
use User\Http\Resources\User\LoginHistoryResource;
use User\Http\Resources\User\PasswordHistoryResource;
use User\Http\Resources\User\ProfileDetailsResource;
use User\Http\Resources\User\UserBlockHistoryResource;
use User\Jobs\EmailJob;
use User\Mail\Admin\PasswordResetEmail;
use User\Mail\Admin\TransactionPasswordResetEmail;
use User\Mail\User\UserAccountHasBeenActivatedEmail;
use User\Mail\User\UserAccountHasBeenDeactivatedEmail;
use User\Mail\User\SuccessfulEmailVerificationEmail;
use User\Mail\User\UserAccountHasBeenFrozenEmail;
use User\Mail\User\UserAccountHasBeenUnfrozenEmail;
use User\Models\User;
use User\Services\UserAdminService;
use User\Support\UserActivityHelper;


class UserController extends Controller
{
    private $user_admin_service;

    public function __construct(UserAdminService $user_admin_service)
    {
        $this->user_admin_service = $user_admin_service;
    }

    /**
     * Get user's list
     * @group
     * Admin > User
     * @param UserListRequest $request
     * @return JsonResponse
     */
    public function index(UserListRequest $request)
    {
        $list = User::query()->filter()->orderByDesc('id')->paginate();
        return api()->success(null, [
            'list' => ProfileDetailsResource::collection($list),
            'pagination' => [
                'total' => $list->total(),
                'per_page' => $list->perPage(),
            ]
        ]);
    }

    /**
     * Get user data
     * @group
     * Admin > User
     * @param GetUserDataRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function getUser(GetUserDataRequest $request)
    {
        try {
            return api()->success(null,ProfileDetailsResource::make(User::query()->where('member_id','=',$request->get('user_id'))->first()));
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * Block Or Unblock User Account
     * @group
     * Admin > User
     * @param BlockOrUnblockUser $request
     * @return JsonResponse
     */
    public function blockOrUnblockUser(BlockOrUnblockUser $request)
    {

        $user = User::whereMemberId($request->get('user_id'))->first();
        if ($user->id == auth()->user()->id)
            return api()->error(trans('user.responses.you-cant-block-unblock-your-account'));
        if ($request->get('block')) {
            $user->block_type = USER_BLOCK_TYPE_BY_ADMIN;
            $user->block_reason = $request->has('block_reason') ? $request->get('block_reason') : trans('user.responses.user-account-deactivated-by-admin');
        } else {
            $user->block_type = null;
            $user->block_reason = trans('user.responses.user-account-activated-by-admin');

        }
        $user->save();
        $user->signOut();

        return api()->success(trans('user.responses.ok'));
    }

    /**
     * Activate or Deactivate user account
     * @group
     * Admin > User
     * @param ActivateOrDeactivateUserAccount $request
     * @return JsonResponse
     */
    public function activateOrDeactivateUserAccount(ActivateOrDeactivateUserAccount $request)
    {
        $user = User::whereMemberId($request->get('user_id'))->first();
        if ($user->id == auth()->user()->id)
            return api()->error(trans('user.responses.you-cant-deactivate-active-your-account'));
        if ($request->get('status') == 'activate') {
            $user->update([
                'is_deactivate' => false
            ]);
            EmailJob::dispatch(new UserAccountHasBeenDeactivatedEmail($user), $user->email);
            return api()->success(trans('user.responses.user-account-activate-successfully'));
        } else if ($request->get('status') == 'deactivate') {

            $user->update([
                'is_deactivate' => true
            ]);

            $user->signOut();
            EmailJob::dispatch(new UserAccountHasBeenActivatedEmail($user), $user->email);
            return api()->success(trans('user.responses.user-account-deativate-successfully'));
        }

        return api()->error(trans('user.responses.global-error'), null, 400);
    }

    /**
     * Freeze or Unfreeze user account
     * @group
     * Admin > User
     * @param FreezeOrUnfreezeUserAccountRequest $request
     * @return JsonResponse
     */
    public function freezeOrUnfreezeUserAccount(FreezeOrUnfreezeUserAccountRequest $request)
    {
        $user = User::whereMemberId($request->get('user_id'))->first();
        if ($user->id == auth()->user()->id)
            return api()->error(trans('user.responses.you-cant-freeze-unfreeze-your-account'));
        if ($request->get('status') == 'freeze') {
            $user->update([
                'is_freeze' => true
            ]);
            EmailJob::dispatch(new UserAccountHasBeenFrozenEmail($user), $user->email);
            return api()->success(trans('user.responses.user-account-frozen-successfully'));
        }
        if ($request->get('status') == 'unfreeze') {
            $user->update([
                'is_freeze' => false,
            ]);
            EmailJob::dispatch(new UserAccountHasBeenUnfrozenEmail($user), $user->email);
            return api()->success(trans('user.responses.user-account-unfreeze-successfully'));
        }

        return api()->error(trans('user.responses.global-error'), null, 400);
    }

    /**
     * Verify Email User Account
     * @group
     * Admin > User
     * @param VerifyUserEmailRequest $request
     * @return JsonResponse
     */
    public function verifyUserEmailAccount(VerifyUserEmailRequest $request)
    {

        $user = User::whereMemberId($request->get('member_id'))->first();
        if (!$user->isEmailVerified()) {
            $user->email_verified_at = now();
            $user->save();

            list($ip_db, $agent_db) = UserActivityHelper::getInfo($request);
            EmailJob::dispatch(new SuccessfulEmailVerificationEmail($user, $ip_db, $agent_db), $user->email);
        }
        return api()->success(trans('user.responses.ok'));
    }


    /**
     * User Password History
     * @group
     * Admin > User History
     * @param HistoryRequest $request
     * @return JsonResponse
     */
    public function passwordHistory(HistoryRequest $request)
    {
        return api()->success(trans('user.responses.ok'), PasswordHistoryResource::collection(User::whereMemberId($request->get('user_id'))->first()->userHistories('password')->get()));
    }

    /**
     * User Block History
     * @group
     * Admin > User History
     * @param HistoryRequest $request
     * @return JsonResponse
     */
    public function blockHistory(HistoryRequest $request)
    {
        return api()->success(trans('user.responses.ok'), UserBlockHistoryResource::collection(User::whereMemberId($request->get('user_id'))->first()->userHistories('block_type')->get()));
    }

    /**
     * User Login History
     * @group
     * Admin > User History
     * @param HistoryRequest $request
     * @return JsonResponse
     */
    public function loginHistory(HistoryRequest $request)
    {
        return api()->success(trans('user.responses.ok'), LoginHistoryResource::collection(User::whereMemberId($request->get('user_id'))->first()->loginAttempts));
    }

    /**
     * User Email Verification History
     * @group
     * Admin > User History
     * @param HistoryRequest $request
     * @return JsonResponse
     */
    public function emailVerificationHistory(HistoryRequest $request)
    {
        return api()->success(trans('user.responses.ok'), OtpResource::collection(User::whereMemberId($request->get('user_id'))->first()->otps()->where('type', OTP_TYPE_EMAIL_VERIFICATION)->get()));
    }

    /**
     * create user by super admin
     * @group
     * Admin > User
     * @param CreateAdminRequest $request
     * @return JsonResponse
     */
    public function createUserByAdmin(CreateAdminRequest $request)
    {
        $this->user_admin_service->createAdmin($request);
        return api()->success(trans('user.responses.ok'), null);

    }

    /**
     * Update user
     * @group
     * Admin > User
     * @param UserUpdateRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(UserUpdateRequest $request)
    {
        try {
            $user = User::whereMemberId($request->get('member_id'))->first();
            $user->update($request->validated());
            $user->refresh();
            return api()->success(trans('responses.ok'), ProfileDetailsResource::make($user));
        } catch (\Throwable $e) {
            throw $e;
        }

    }

    /**
     * Update user avatar
     * @group
     * Admin > User
     * @param UpdateUserAvatarRequest $request
     * @return JsonResponse
     */
    public function updateAvatar(UpdateUserAvatarRequest $request)
    {
        $user = User::whereMemberId($request->get('member_id'))->first();
        $fileName = $user->id . '-' . $user->member_id . '-' . mt_rand(100,99999) . '-' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
        $mimeType = $request->file('avatar')->getMimeType();
        $path = $request->file('avatar')->storeAs('avatars', $fileName ,'s3');
        $user->update([
            'avatar' => [
                'file_name' => $fileName,
                'mime' => $mimeType,
                'url' => Storage::disk('s3')->url($path)
            ]
        ]);

        return api()->success();
    }

    /**
     * Reset password
     * @group
     * Admin > User
     * @param MemberIdRequest $request
     * @return JsonResponse
     */
    public function resetPassword(MemberIdRequest $request)
    {
        $user = User::query()->whereMemberId($request->get('member_id'))->first();
        $new_password = strtolower(Str::random(8));

        $user->update([
            'password' => $new_password
        ]);

        EmailJob::dispatch(new PasswordResetEmail($user,$new_password), $user->email);

        return api()->success();
    }


}
