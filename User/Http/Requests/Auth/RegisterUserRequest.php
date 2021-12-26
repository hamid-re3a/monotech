<?php

namespace User\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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

    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'min:1', 'max:255', 'regex:/^[a-zA-Z ]*$/'],
            'last_name' => ['required', 'string', 'min:1', 'max:255', 'regex:/^[a-zA-Z ]*$/'],
            'gender' => 'nullable|in:Male,Female,Other',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'username' => ['required', 'unique:users,username', 'regex:/^[a-z][a-z0-9_]{2,}$/'],
            'password' => ['required', 'regex:/' . getSetting('USER_REGISTRATION_PASSWORD_CRITERIA') . '/'],
            'password_confirmation' => 'required|string|same:password',
        ];
    }

    public function messages()
    {
        return [
            'password_confirmation.same' => trans('user.validation.password-same'),
            'first_name.required' => trans('user.validation.first-name-required'),
            'last_name.required' => trans('user.validation.last-name-required'),
            'email.required' => trans('user.validation.email-required'),
            'email.unique:users' => trans('user.validation.email-unique'),
            'username.unique:users' => trans('user.validation.username-unique'),
            'username.required' => trans('user.validation.username-required'),
            'username.regex' => trans('user.validation.username-regex'),
            'password.required' => trans('user.validation.first-name-required'),
            'email.email' => trans('user.validation.email-is-incorrect'),
        ];
    }
}
