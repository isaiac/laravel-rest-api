<?php

namespace App\Http\Requests\Role;

use Dingo\Api\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:App\Models\Role',

            'permissions' => 'sometimes|array',
            'permissions.*' => 'sometimes|required_array_keys:id',
            'permissions.*.id' => 'sometimes|required|string|max:255|exists:App\Models\Permission'
        ];
    }
}
