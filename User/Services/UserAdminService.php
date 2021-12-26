<?php


namespace User\Services;


use App\Jobs\User\UserDataJob;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use User\Repository\RoleRepository;
use User\Repository\UserRepository;
use Exception;

class UserAdminService
{
    private $user_repository;
    private $role_repository;

    public function __construct(UserRepository $user_repository, RoleRepository $role_repository)
    {
        $this->user_repository = $user_repository;
        $this->role_repository = $role_repository;
    }

    /**
     * create admin user and produce on rabbitMQ
     * @param $request
     * @return mixed
     */
    public function createAdmin($request)
    {
        $admin = $this->user_repository->createAdmin($request);
        $role = $this->role_repository->getRole($request->role_id);
        $admin->assignRole($role->id);
        if($role->name != USER_ROLE_CLIENT)
            $admin->assignRole(USER_ROLE_CLIENT);
        return $admin;

    }

    /**
     * get userService Object by id
     * @param $user_update
     * @return mixed
     */
    public function getUserData($user_update){
        return $this->user_repository->getUserData($user_update->getId());
    }


    public function update($request)
    {
        return $this->user_repository->update($request);

    }


}
