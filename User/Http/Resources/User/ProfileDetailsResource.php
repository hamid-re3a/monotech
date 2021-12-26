<?php

namespace User\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
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
            'member_id' => $user->member_id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
            'username' => $user->username,
            'mobile_number' => $user->mobile_number,
            'landline_number' => $user->landline_number,
            'address_line1' => $user->address_line1,
            'address_line2' => $user->address_line2,
            'sponsor' => $user->sponsor()->exists() ? $user->sponsor->username : null,
            'placement' => null,
            'status' => null,
            'genealogy' => null,
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'gender' => $user->gender,
            'birthday' => $user->birthday ? $user->birthday->format('Y/m/d') : null,
            'avatar' => $user->getAvatarBase64(),
            'state' => $user->state_id ? $user->state->name : null,
            'city' => $user->city_id ? $user->city->name  : null,
            'country' => $user->country_id ? $user->country->name : null,
            'zip_code' => $user->zip_code ? $user->zip_code  : null,
            'roles' => $user->getRoleNames()->first()
        ];
    }
}
