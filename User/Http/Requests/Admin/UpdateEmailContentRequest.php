<?php

namespace User\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailContentRequest extends FormRequest
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
            'key' => 'required|string|exists:email_content_settings,key',
            'subject' => 'required|string',
            'body' => 'required|string',
            'is_active' => 'required|boolean',
//            'from' => 'required|string|email',
//            'from_name' => 'required|string|',
//            'variables' => 'required|string',
//            'variables_description' => 'required|string',
//            'type' => 'required|in:email',
        ];
    }
}
