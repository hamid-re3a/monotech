<?php

namespace User\Http\Controllers\Front;


use Illuminate\Routing\Controller;
use User\Http\Resources\User\ActivityResource;
use User\Models\User;
use User\Repository\ActivityRepository;

class ActivityController extends Controller
{
    private $activity_repository;
    public function __construct(ActivityRepository $activity_repository)
    {
        $this->activity_repository = $activity_repository;
    }

    /**
     * Get user activities list
     * @group Public User > Activities
     */
    public function index()
    {
        /**@var $user User*/
        $user = auth()->user();
        $list = $this->activity_repository->getPaginated($user->id);

        return api()->success(null,[
            'list' => ActivityResource::collection($list),
            'pagination' => [
                'total' => $list->total(),
                'per_page' => $list->perPage()
            ]
        ]);
    }

}
