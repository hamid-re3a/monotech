<?php

namespace User\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'min:1', 'max:255', 'regex:/^[a-zA-Z ]*$/'],
            'last_name' => ['required', 'string', 'min:1', 'max:255', 'regex:/^[a-zA-Z ]*$/'],
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'username' => ['required', 'unique:users', 'regex:/^[a-z][a-z0-9_]{2,}$/'],
            'password' => ['required', 'regex:/' . getSetting('USER_REGISTRATION_PASSWORD_CRITERIA') . '/'],
            'password_confirmation' => 'required|string|same:password',
            'role_id' => 'required|numeric|exists:roles,id',
            'sponsor_id' => 'required|numeric|exists:users,id',
        ];
    }
}
