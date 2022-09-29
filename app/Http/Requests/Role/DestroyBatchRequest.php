<?php

namespace App\Http\Requests\Role;

use Dingo\Api\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

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
        Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[a-z0-9]+(?:[_-][a-z0-9]+)*$/', $value);
        }, trans('validation.slug'));

        return [
            'data' => 'required|array',
            'data.*.id' => 'required|slug|max:255|exists:App\Models\Role'
        ];
    }
}
