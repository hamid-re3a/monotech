<?php

namespace User\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class EmailExistenceRequest extends FormRequest
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
            'email' => 'required|string|email:rfc,dns|max:255',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => trans('user.validation.email-required'),
            'email.email' => trans('user.validation.email-is-incorrect'),
        ];
    }
}
