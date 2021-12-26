<?php

namespace User\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use User\Models\User;

class UpdateUserAvatarRequest extends FormRequest
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
            'member_id' => 'required|integer|exists:users,member_id',
            'avatar' => 'required|file|mimes:png,jpg,jpeg|max:2048',
        ];
    }

}
