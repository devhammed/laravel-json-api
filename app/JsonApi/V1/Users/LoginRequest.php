<?php

namespace App\JsonApi\V1\Users;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

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
            'data.type'                   => 'type',
            'data.attributes.email'       => 'email',
            'data.attributes.password'    => 'password',
            'data.attributes.token_name'  => 'token name',
        ];
    }
}
