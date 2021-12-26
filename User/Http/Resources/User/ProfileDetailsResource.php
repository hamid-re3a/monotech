<?php

namespace User\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use User\Models\User;

class ProfileDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function toArray($request)
    {
        /**@var $user User */
        $user = $this;

        return [
            'id' => $user->id,
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'wallet' => [
                'id' => $user->wallet->id,
                'balance' => $user->balance,
                'updated_at' => $user->wallet->updated_at,
            ]
        ];
    }
}
