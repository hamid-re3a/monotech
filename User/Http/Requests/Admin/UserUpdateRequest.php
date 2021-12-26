<?php

namespace User\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use User\Models\Country;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \Exception
     */
    public function rules()
    {
        return [
            'member_id' => 'required|exists:users,member_id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'nullable|in:Male,Female,Other',
            'birthday' => 'nullable|date|before:' . now() . '|date_format:Y/m/d',
            'country_id' => 'nullable|integer|exists:countries,id',
            'state_id' => 'nullable|integer|exists:cities,id,country_id,' . $this->get('country_id'),
            'city_id' => 'nullable|integer|exists:cities,id,country_id,' . $this->get('country_id') . ',parent_id,' . $this->get('state_id'),
            'mobile_number' => 'nullable|string|phone:' . $this->getCountryIso(),
            'landline_number' => 'nullable|string|phone:' . $this->getCountryIso(),
            'address_line1' => "nullable|string",
            'address_line2' => "nullable|string",
            'zip_code' => "nullable|string",

        ];
    }

    private function getCountryIso()
    {
        if($this->has('country_id')) {
            $country = Country::find($this->get('country_id'));
            if($country)
                return $country->iso2;
        }

        return null;
    }
}
