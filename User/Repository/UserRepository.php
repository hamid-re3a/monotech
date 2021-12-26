<?php

namespace User\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use User\Models\User;

class UserRepository
{
    private $entity_name = User::class;

    public function createAdmin($request)
    {
        $user_entity = new $this->entity_name;
        $user_entity->first_name = $request->first_name;
        $user_entity->last_name = $request->last_name;
        $user_entity->email = $request->email;
        $user_entity->email_verified_at = now();
        $user_entity->username = $request->username;
        $user_entity->sponsor_id = $request->sponsor_id;
        $user_entity->password = encrypt($request->password);
        $user_entity->save();
        return $user_entity;
    }

    public function getUserData($id): \User\Services\Grpc\User
    {
        /** @var $user User */
        $user = new $this->entity_name;
        $user = $user->query()->whereId($id)->first();
        return !is_null($user) ? $user->getGrpcMessage() : new \User\Services\Grpc\User;
    }

    public function getUserDataByMemberId($member_id): \User\Services\Grpc\User
    {
        /** @var $user User */
        $user = new $this->entity_name;
        $user = $user->query()->where('member_id','=',$member_id)->first();
        return !is_null($user) ? $user->getGrpcMessage() : new \User\Services\Grpc\User;
    }

    public function getUserWallet($id, $crypto_name)
    {
        /** @var $user User */
        $user = new $this->entity_name;
        $user = $user->query()->whereId($id)->first();

        if (!$user)
            return false;
        if (
        $wallet = $user->wallets()->active()->whereHas('cryptoCurrency', function (Builder $query) use ($crypto_name) {
            $query->where('iso', '=', $crypto_name);
        })->first()
        )
            return $wallet;

        return false;
    }

    public function getUsersCount()
    {
        /**@var $model User*/
        $model = new $this->entity_name;
        return $model->query()->count();
    }


}
