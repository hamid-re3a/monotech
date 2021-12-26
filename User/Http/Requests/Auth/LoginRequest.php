<?php

namespace User\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     */
    public function rules()
    {
        return [
            'email' => 'required|string|max:255',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [

            'email.exists' => trans('user.validation.email-not-exists'),
            'email.required' => trans('user.validation.email-required'),
            'password.required' => trans('user.validation.first-name-required'),
            'email.email' => trans('user.validation.email-is-incorrect'),
        ];
    }
}
