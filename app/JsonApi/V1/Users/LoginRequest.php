<?php

namespace App\JsonApi\V1\Users;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            'data' => [
                'required',
                'array',
            ],
            'data.type' => [
                'required',
                'in:users',
            ],
            'data.attributes' => [
                'required',
                'array',
            ],
            'data.attributes.token_name' => [
                'required',
                'string',
                'max:100',
            ],
            'data.attributes.email' => [
                'required',
                'string',
                'email',
            ],
            'data.attributes.password' => [
                'required',
                'string',
                'min:8',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'data.type'                   => __('form.type'),
            'data.attributes'             => __('form.attributes'),
            'data.attributes.email'       => __('form.email'),
            'data.attributes.password'    => __('form.password'),
            'data.attributes.token_name'  => __('form.token_name'),
        ];
    }
}
