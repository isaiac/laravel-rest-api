<?php

namespace App\Http\Requests\User;

use Dingo\Api\Http\FormRequest;

class DestroyBatchRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'data' => 'required|array',
            'data.*.id' => 'required|uuid|exists:App\Models\User'
        ];
    }
}
