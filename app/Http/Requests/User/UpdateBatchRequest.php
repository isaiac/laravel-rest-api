<?php

namespace App\Http\Requests\User;

use App\Enums\UserStatus;
use Dingo\Api\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class UpdateBatchRequest extends FormRequest
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
        Validator::extend('unique_ignore_param', function ($attribute, $value, $parameters, $validator) {
            preg_match('/^(?<prefix>.*)\..+$/', $attribute, $matches);

            $request_param = isset($matches['prefix']) ? $matches['prefix'].'.'.$parameters[2] : $parameters[2];
            $parameters[2] = Arr::get($validator->getData(), $request_param);

            return $validator->validateUnique($attribute, $value, $parameters);
        }, trans('validation.unique'));

        return [
            'data' => 'required|array',
            'data.*.id' => 'required|uuid|exists:App\Models\User',
            'data.*.name' => 'sometimes|string|max:255',
            'data.*.email' => 'sometimes|required|email|max:255|unique_ignore_param:App\Models\User,email,id',
            'data.*.username' => 'sometimes|required|string|min:3|max:20|unique_ignore_param:App\Models\User,username,id',
            'data.*.password' => 'sometimes|required|string|min:6|max:20|confirmed',
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
