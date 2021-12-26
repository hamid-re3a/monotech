<?php

namespace Promotion\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreatePromotionRequest extends FormRequest
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
            'start_date' => 'required|date',
            'end_date' => 'required|date|before_or_equal:start_date',
            'amount' => 'required|numeric|min:1',
            'quota' => 'required|numeric|min:1',
        ];
    }
}
