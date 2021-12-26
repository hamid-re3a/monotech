<?php

namespace User\Http\Requests\User\profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use User\Models\User;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required',
            'password' => 'required|different:current_password|regex:/' . getSetting('USER_REGISTRATION_PASSWORD_CRITERIA') . '/',
            'password_confirmation' => 'required|same:password'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $user = User::whereEmail($this->get('email'))->first();

        if($user) {
            $validator->after(function ($validator) use($user) {
                if ( Hash::check($this->current_password, $user->password) ) {
                    $validator->errors()->add('current_password', 'Your current password is incorrect.');
                }

                if(getSetting("USER_CHECK_PASSWORD_HISTORY_FOR_NEW_PASSWORD"))
                    if(request()->user()->historyCheck('password',$this->password))
                        $validator->errors()->add('password', trans('user.responses.password-already-used-by-you-try-another-one'));

            });
        }
        return;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'current_password.required' => 'Enter current password.',
            'password.required' => 'Enter new password.',
            'password_confirmation.required' => 'Confirm new password.',
            'password_confirmation.same' => 'Password does not match.',
        ];
    }
}
