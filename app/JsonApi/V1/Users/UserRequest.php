<?php

namespace App\JsonApi\V1\Users;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class UserRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        $model       = $this->model();
        $uniqueRule  = Rule::unique('users');

        if ($model) {
            $uniqueRule->ignoreModel($model);
        }

        $rules = [
            'name'    => [
                'required',
                'string',
                'max:255',
            ],
            'email'   => [
                'required',
                'string',
                'email',
                'max:255',
                $uniqueRule,
            ],
            'password' => [
                $model ? 'sometimes' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ];

        if (!$model) {
            $rules['token_name'] = [
                'required',
                'string',
                'max:255',
            ];
        }

        return $rules;
    }
}
