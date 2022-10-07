<?php

namespace App\Http\Requests\User;

use App\Enums\UserStatus;
use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:App\Models\User,email,'.$this->user->id,
            'username' => 'sometimes|required|string|min:3|max:20|unique:App\Models\User,username,'.$this->user->id,
            'password' => 'sometimes|required|string|min:6|max:20|confirmed',
            'status' => ['sometimes', 'required', new Enum(UserStatus::class)],

            'roles' => 'sometimes|array',
            'roles.*' => 'sometimes|required_array_keys:id',
            'roles.*.id' => 'sometimes|required|string|max:255|exists:App\Models\Role',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'sometimes|required_array_keys:id',
            'permissions.*.id' => 'sometimes|required|string|max:255|exists:App\Models\Permission'
        ];
    }
}
