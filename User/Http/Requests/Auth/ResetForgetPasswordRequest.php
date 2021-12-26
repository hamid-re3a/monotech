<?php

namespace User\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use User\Models\User;

class ResetForgetPasswordRequest extends FormRequest
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
            'email' => 'required|email:rfc,dns|exists:users,email',
            'otp' => 'required|string|exists:otps,otp',
            'password' => 'required|regex:/' . getSetting('USER_REGISTRATION_PASSWORD_CRITERIA') . '/|confirmed',
        ];
    }

    public function messages()
    {
        return [

            'email.exists' => trans('user.validation.email-not-exists'),
            'email.required' => trans('user.validation.email-required'),
            'password.required' => trans('user.validation.first-name-required'),
            'email.email' => trans('user.validation.email-is-incorrect'),
            'otp.exists' => trans('user.responses.password-reset-code-is-invalid'),
        ];
    }

    public function withValidator($validator)
    {
        $user = User::whereEmail($this->email)->first();
        if($user) {
            $validator->after(function ($validator) use($user){
                    if ( Hash::check($this->password, $user->password) ) {
                        $validator->errors()->add('password', trans('user.responses.password-already-used-by-you-try-another-one'));
                    }

                    if(getSetting("USER_CHECK_PASSWORD_HISTORY_FOR_NEW_PASSWORD"))
                        if($user->historyCheck('password',$this->password))
                            $validator->errors()->add('password', trans('user.responses.password-already-used-by-you-try-another-one'));
            });
        }
        return;
    }
}
