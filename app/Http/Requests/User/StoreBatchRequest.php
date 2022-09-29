<?php

namespace App\Http\Requests\User;

use App\Enums\UserStatus;
use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreBatchRequest extends FormRequest
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
            'data.*.name' => 'sometimes|string|max:255',
            'data.*.email' => 'required|email|max:255|unique:App\Models\User',
            'data.*.username' => 'required|string|min:3|max:20|unique:App\Models\User',
            'data.*.password' => 'required|string|min:6|max:20|confirmed',
            'data.*.status' => ['sometimes', 'required', new Enum(UserStatus::class)],

            'data.*.roles' => 'sometimes|array',
            'data.*.roles.*' => 'sometimes|required_array_keys:id',
            'data.*.roles.*.id' => 'sometimes|required|string|max:255|exists:App\Models\Role',
            'data.*.permissions' => 'sometimes|array',
            'data.*.permissions.*' => 'sometimes|required_array_keys:id',
            'data.*.permissions.*.id' => 'sometimes|required|string|max:255|exists:App\Models\Permission'
        ];
    }
}
