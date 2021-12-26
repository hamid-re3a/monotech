<?php

namespace User\Http\Controllers;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use User\Http\Requests\Globally\CitiesRequest;
use User\Http\Requests\Globally\StatesRequest;
use User\Http\Resources\CityResource;
use User\Http\Resources\CountryResource;
use User\Http\Resources\General\ProfileDetailsResource;
use User\Models\City;
use User\Models\Country;
use User\Models\User;

class GeneralController extends Controller
{

    /**
     * Countries list
     * @group General
     * @unauthenticated
     * @return \Illuminate\Http\JsonResponse
     */
    public function countries()
    {
        return api()->success(null,CountryResource::collection(Country::all()));
    }

    /**
     * States list
     * @group General
     * @unauthenticated
     * @param StatesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function states(StatesRequest $request)
    {
        $country = Country::query()->find($request->get('country_id'));
        return api()->success(null,CityResource::collection($country->states()->get()));
    }

    /**
     * Cities list
     * @group General
     * @unauthenticated
     * @param CitiesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cities(CitiesRequest $request)
    {
        $state = City::query()->where('id',$request->get('state_id'))->with('cities')->first();

        return api()->success(null,CityResource::collection($state->cities));
    }

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

    /**
     * Get avatar details
     * @group General
     * @unauthenticated
     * @queryParam member_id required integer
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvatarDetails($member_id)
    {
        $user = User::query()->where('member_id', $member_id)->get()->first();
        if(!$user)
            return api()->error(trans('user.responses.invalid-member-id'),null,404);

        if(empty($user->avatar))
            return api()->error(trans('user.responses.user-has-no-avatar'),null,404);

        $avatar = json_decode($user->avatar,true);
        return api()->success(null,[
            'mime' => $avatar['mime'],
            'link' => route('customer.general.avatar-image', [
                'member_id' => $member_id
            ])
        ]);
    }

    /**
     * Get avatar image
     * @group General
     * @unauthenticated
     * @queryParam member_id required integer
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getAvatarImage($member_id)
    {
        /**@var $user User */
        $user = User::query()->where('member_id', $member_id)->get()->first();

        if(!$user)
            return api()->notFound();

        $avatar = $user->getAvatarBase64();

        return $avatar == null ? api()->notFound() : $avatar;
    }

    /**
     * Get avatar image file
     * @group General
     * @unauthenticated
     * @queryParam member_id required integer
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getAvatarFile($member_id)
    {
        /**@var $user User */
        $user = User::query()->where('member_id', $member_id)->get()->first();

        if(!$user)
            return api()->notFound();

        $avatar = $user->getAvatarFile();

        return $avatar == null ? api()->notFound() : $avatar;
    }
}
