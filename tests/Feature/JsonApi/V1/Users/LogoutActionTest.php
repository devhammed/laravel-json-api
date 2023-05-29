<?php

namespace Tests\Feature\JsonApi\V1\Users;

use App\Models\User;
use Tests\TestCase;

class LogoutActionTest extends TestCase
{
    public function test_cannot_logout_when_not_logged_in(): void
    {
        $route = route('api.v1.users.logout');

        $response = $this->jsonApi()
            ->post($route);

        $response->assertUnauthorized();

        $response->assertJson([
            'jsonapi' => [
                'version' => '1.0',
            ],
            'errors' => [
                [
                    'status' => '401',
                ],
            ],
        ]);
    }

    public function test_can_logout_when_logged_in(): void
    {
        $user = User::factory()->create();

        $route = route('api.v1.users.logout');

        $token = $user->createToken('test');

        $tableData = [
            'id'             => $token->accessToken->id,
            'tokenable_type' => User::class,
            'tokenable_id'   => $user->getRouteKey(),
            'name'           => 'test',
        ];

        $this->assertDatabaseHas('personal_access_tokens', $tableData);

        $response = $this->jsonApi()
            ->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
            ->post($route);

        $response->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', $tableData);
    }
}
