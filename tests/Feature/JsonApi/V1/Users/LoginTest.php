<?php

namespace Tests\Feature\JsonApi\V1\Users;

use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    public function test_has_validation_errors(): void
    {
        $route = route('api.v1.users.login');

        $response = $this->jsonApi()
            ->withData([
                'type'       => 'users',
                'attributes' => [
                    'email'      => '',
                    'password'   => '',
                    'token_name' => '',
                ],
            ])
            ->post($route);

        $response->assertUnprocessable();

        $response->assertJson([
            'jsonapi' => [
                'version' => '1.0',
            ],
            'errors' => [
                [
                    'status' => '422',
                    'source' => [
                        'pointer' => '/data/attributes/token_name',
                    ],
                    'title'  => __('jsonapi-validation::validation.invalid.title'),
                    'detail' => __('validation.required', [
                        'attribute' => 'token name',
                    ]),
                ],
                [
                    'status' => '422',
                    'source' => [
                        'pointer' => '/data/attributes/email',
                    ],
                    'title'  => __('jsonapi-validation::validation.invalid.title'),
                    'detail' => __('validation.required', [
                        'attribute' => 'email',
                    ]),
                ],
                [
                    'status' => '422',
                    'source' => [
                        'pointer' => '/data/attributes/password',
                    ],
                    'title'  => __('jsonapi-validation::validation.invalid.title'),
                    'detail' => __('validation.required', [
                        'attribute' => 'password',
                    ]),
                ],
            ],
        ]);
    }

    public function test_cannot_login_with_incorrect_credentials(): void
    {
        $user = User::factory()->create();

        $route = route('api.v1.users.login');

        $response = $this->jsonApi()
            ->withData([
                'type'       => 'users',
                'attributes' => [
                    'email'      => $user->email,
                    'password'   => 'wrong-password',
                    'token_name' => 'test',
                ],
            ])
            ->post($route);

        $response->assertUnauthorized();

        $response->assertJson([
            'jsonapi' => [
                'version' => '1.0',
            ],
            'errors' => [
                [
                    'status'  => '401',
                    'detail'  => __('auth.failed'),
                    'title'   => __('auth.invalid'),
                ],
            ],
        ]);
    }

    public function test_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create();

        $route = route('api.v1.users.login');

        $response = $this->jsonApi()
            ->withData([
                'type'       => 'users',
                'attributes' => [
                    'email'      => $user->email,
                    'password'   => 'password',
                    'token_name' => 'test',
                ],
            ])
            ->post($route);

        $response->assertOk();

        $response->assertJson([
            'jsonapi' => [
                'version' => '1.0',
            ],
            'data' => [
                'type'       => 'users',
                'id'         => (string)$user->getRouteKey(),
                'attributes' => [
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'created_at' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at->toISOString(),
                ],
            ],
        ]);

        $response->assertJsonStructure([
            'meta' => [
                'access_token' => [
                    'type',
                    'name',
                    'value',
                ],
            ],
        ]);

        $response->assertJsonPath('meta.access_token.type', 'Bearer');

        $response->assertJsonPath('meta.access_token.name', 'test');

        $this->assertArrayHasKey('value', $response->json('meta.access_token'));

        [$id, $token] = explode('|', $response->json('meta.access_token.value'), 2);

        $this->assertIsString($token);

        $this->assertDatabaseHas('personal_access_tokens', [
            'id'             => $id,
            'tokenable_type' => User::class,
            'tokenable_id'   => $user->getRouteKey(),
            'name'           => 'test',
            'last_used_at'   => null,
        ]);
    }
}
