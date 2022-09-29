<?php

namespace App\Http\Requests\Role;

use Dingo\Api\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

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
        Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[a-z0-9]+(?:[_-][a-z0-9]+)*$/', $value);
        }, trans('validation.slug'));

        Validator::extend('unique_ignore_param', function ($attribute, $value, $parameters, $validator) {
            preg_match('/^(?<prefix>.*)\..+$/', $attribute, $matches);

            $request_param = isset($matches['prefix']) ? $matches['prefix'].'.'.$parameters[2] : $parameters[2];
            $parameters[2] = Arr::get($validator->getData(), $request_param);

            return $validator->validateUnique($attribute, $value, $parameters);
        }, trans('validation.unique'));

        return [
            'data.*.id' => 'required|slug|max:255|exists:App\Models\Role',
            'data.*.name' => 'sometimes|required|string|max:255|unique_ignore_param:App\Models\Role,name,id',

            'data.*.permissions' => 'sometimes|array',
            'data.*.permissions.*' => 'sometimes|required_array_keys:id',
            'data.*.permissions.*.id' => 'sometimes|required|string|max:255|exists:App\Models\Permission'
        ];
    }
}
