<?php

namespace App\JsonApi\V1\Posts;

use App\Rules\Slug;
use Illuminate\Validation\Rule;
use LaravelJsonApi\Validation\Rule as JsonApiRule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class PostRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        $post       = $this->model();
        $uniqueSlug = Rule::unique('posts', 'slug');

        if ($post !== null) {
            $uniqueSlug->ignoreModel($post);
        }

        return [
            'title' => [
                'required',
                'string',
            ],
            'content' => [
                'required',
                'string',
            ],
            'slug' => [
                'sometimes',
                'string',
                new Slug(),
                $uniqueSlug,
            ],
            'publishedAt' => [
                'nullable',
                JsonApiRule::dateTime(),
            ],
            'tags' => JsonApiRule::toMany(),
        ];
    }
}
