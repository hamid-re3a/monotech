<?php

namespace User\Http\Controllers\Admin;


use Illuminate\Routing\Controller;
use User\Http\Resources\User\ProfileDetailsResource;
use User\Models\User;
use User\Repository\UserRepository;


class DashboardController extends Controller
{

    private $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    /**
     * Counts users
     * @group
     * Admin > User
     */
    public function counts()
    {
        return api()->success(null,[
           'all_users_count' => $this->user_repository->getUsersCount()
        ]);
    }


}
