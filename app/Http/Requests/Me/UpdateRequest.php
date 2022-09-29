<?php

namespace App\Http\Requests\Me;

use Dingo\Api\Http\FormRequest;

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
            'email' => 'sometimes|required|email|max:255|unique:App\Models\User,email,'.request()->user()->id,
            'username' => 'sometimes|required|string|min:3|max:20|unique:App\Models\User,username,'.request()->user()->id,
            'password' => 'sometimes|required|string|min:6|max:20|confirmed'
        ];
    }
}
