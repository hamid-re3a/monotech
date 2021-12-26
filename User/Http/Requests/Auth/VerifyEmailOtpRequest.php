<?php

namespace User\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailOtpRequest extends FormRequest
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
            'email' => 'required|email:rfc,dns|exists:users,email',
            'otp' => 'required|string|exists:otps,otp',
        ];
    }

    public function messages()
    {
        return [
            'otp.exists' => trans('user.responses.email-verification-code-is-incorrect'),
            'email.required' => trans('user.validation.email-required'),
            'email.exists' => trans('user.validation.email-not-exists'),
            'email.email' => trans('user.validation.email-is-incorrect'),
        ];
    }
}
