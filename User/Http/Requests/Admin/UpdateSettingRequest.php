<?php

namespace User\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use function Doctrine\Common\Cache\Psr6\get;

class UpdateSettingRequest extends FormRequest
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
            'key' => 'required|string|exists:settings,key',
            'value' => $this->prepareValueValidation(),
            'description' => 'nullable|string',
//            'category' => 'nullable|string'
        ];
    }

    private function prepareValueValidation()
    {
        if ($this->has('key')) {
            switch ($this->get('key')) {
                case 'APP_NAME' :
                case 'USER_REGISTRATION_PASSWORD_CRITERIA':
                default:
                    return 'required|string';
                    break;
                case 'OTP_CONTAIN_ALPHABET_LOWER_CASE':
                case 'USER_CHECK_PASSWORD_HISTORY_FOR_NEW_PASSWORD':
                case 'USER_CHECK_TRANSACTION_PASSWORD_HISTORY_FOR_NEW_PASSWORD':
                case 'OTP_CONTAIN_ALPHABET' :
                case 'IS_LOGIN_PASSWORD_CHANGE_EMAIL_ENABLE' :
                case 'IS_TRANSACTION_PASSWORD_CHANGE_EMAIL_ENABLE' :
                case 'LOGOUT_CLIENTS_FOR_MAINTENANCE' :
                    return 'required|boolean';
                    break;
                case 'OTP_LENGTH':
                case 'USER_FORGOT_PASSWORD_OTP_DURATION':
                case 'USER_FORGOT_PASSWORD_OTP_TRIES':
                case 'USER_EMAIL_VERIFICATION_OTP_DURATION':
                case 'USER_EMAIL_VERIFICATION_OTP_TRIES':
                case 'USER_CHANGE_TRANSACTION_OTP_DURATION':
                case 'USER_CHANGE_TRANSACTION_OTP_TRIES':
                    return 'required|integer';
                    break;
                case 'SYSTEM_IS_UNDER_MAINTENANCE_FROM_DATE' :
                    if (getSetting('SYSTEM_IS_UNDER_MAINTENANCE_TO_DATE')) {
                        return 'nullable|date|before_or_equal:' . getSetting('SYSTEM_IS_UNDER_MAINTENANCE_TO_DATE');
                    } else {
                        return 'nullable|date';
                    }
                    break;
                case 'SYSTEM_IS_UNDER_MAINTENANCE_TO_DATE' :
                    if (getSetting('SYSTEM_IS_UNDER_MAINTENANCE_FROM_DATE')) {
                        return 'nullable|date|after_or_equal:' . getSetting('SYSTEM_IS_UNDER_MAINTENANCE_FROM_DATE');
                    } else {
                        return 'nullable|date';
                    }
                    break;


            }
        } else {
            return '';
        }
    }

}
