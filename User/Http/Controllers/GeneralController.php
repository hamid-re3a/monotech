<?php

namespace User\Http\Controllers;


use Illuminate\Routing\Controller;
use User\Http\Resources\General\ProfileDetailsResource;
use User\Models\User;

class GeneralController extends Controller
{



    /**
     * Get user details
     * @group General
     * @unauthenticated
     * @queryParam member_id required integer
     */
    public function getUserDetails($member_id)
    {
        $user = User::where('member_id', $member_id)->get()->first();
        if(!$user)
            return api()->error(trans('user.responses.invalid-member-id'),null,404);

        return api()->success(null,ProfileDetailsResource::make($user));

    }



}
