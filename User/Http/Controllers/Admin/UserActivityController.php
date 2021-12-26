<?php

namespace User\Http\Controllers\Admin;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use User\Http\Requests\Admin\MemberIdOrUserRequest;
use User\Http\Resources\Admin\UserActivityResource;
use User\Models\User;
use User\Repository\ActivityRepository;

class UserActivityController extends Controller
{
    private $activity_repository;
    public function __construct(ActivityRepository $activity_repository)
    {
        $this->activity_repository = $activity_repository;
    }

    /**
     * Get all activities list
     * @group Admin User > Activities
     */
    public function index()
    {
        $list = $this->activity_repository->getPaginated();

        return api()->success(null,[
            'list' => UserActivityResource::collection($list),
            'pagination' => [
                'total' => $list->total(),
                'per_page' => $list->perPage()
            ]
        ]);
    }

    /**
     * Get user activities
     * @group Admin User > Activities
     * @param MemberIdOrUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userActivity(MemberIdOrUserRequest $request)
    {
        try {

            $user = null;
            if($request->has('member_id') AND !$request->has('user_id')) {
                $user = User::query()->whereMemberId($request->get('member_id'))->first();
            }
            $user_id = $request->has('user_id') ? $request->get('user_id') : $user->id;
            $list = $this->activity_repository->getPaginated($user_id);

            return api()->success(null,[
                'list' => UserActivityResource::collection($list),
                'pagination' => [
                    'total' => $list->total(),
                    'per_page' => $list->perPage()
                ]
            ]);
        } catch (\Throwable $exception) {
            Log::error('User\Http\Controllers\Admin@userActivity => ' . $exception->getMessage());
            return api()->error(null,[
                'subject' => trans('user.responses.global-error')
            ],500);
        }

    }

}
