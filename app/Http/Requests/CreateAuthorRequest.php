<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAuthorRequest extends FormRequest
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
            'data' => 'required|array',
            'data.type' => 'required|in:authors',
            'data.attributes' => 'required|array',
            'data.attributes.first_name' => 'required|string',
            'data.attributes.last_name' => 'required|string',
            'data.attributes.other_name' => 'string',
        ];
    }
}
