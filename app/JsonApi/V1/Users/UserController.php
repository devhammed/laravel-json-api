<?php

namespace App\JsonApi\V1\Users;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthManager;
use Illuminate\Hashing\HashManager;
use App\Http\Controllers\Controller;
use LaravelJsonApi\Core\Document\Error;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions;

class UserController extends Controller
{
    use Actions\FetchMany;
    use Actions\FetchOne;
    use Actions\Store;
    use Actions\Update;
    use Actions\Destroy;
    use Actions\FetchRelated;
    use Actions\FetchRelationship;
    use Actions\UpdateRelationship;
    use Actions\AttachRelationship;
    use Actions\DetachRelationship;

    /**
     * Handle the "created" hook.
     */
    public function created(
        User $user,
        UserRequest $request,
        UserQuery $query
    ): DataResponse {
        $tokenName = $request->input('data.attributes.token_name');

        $accessToken = $user->createToken($tokenName)->plainTextToken;

        return DataResponse::make($user)
            ->withQueryParameters($query)
            ->withMeta($this->formatTokenMeta($tokenName, $accessToken))
            ->didCreate();
    }

    /**
     * Handle login action.
     */
    public function login(
        HashManager $hash,
        LoginRequest $request,
        UserQuery $query
    ): Error|DataResponse {
        $email = $request->input('data.attributes.email');

        $password = $request->input('data.attributes.password');

        $tokenName = $request->input('data.attributes.token_name');

        if (
            !($user = User::whereEmail($email)->first()) ||
            !($hash->check($password, $user->password))
        ) {
            return Error::make()
                ->setTitle('Invalid credentials')
                ->setStatus(Response::HTTP_UNAUTHORIZED)
                ->setDetail('The provided credentials are invalid.');
        }

        $accessToken = $user->createToken($tokenName)->plainTextToken;

        return DataResponse::make($user)
            ->withQueryParameters($query)
            ->withMeta($this->formatTokenMeta($tokenName, $accessToken));
    }

    /**
     * Handle logout action.
     */
    public function logout(AuthManager $auth): Response
    {
        $auth->user()
            ->currentAccessToken()
            ->delete();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Format token meta.
     */
    protected function formatTokenMeta(
        string $tokenName,
        string $accessToken
    ): array {
        return [
            'access_token' => [
                'type'       => 'Bearer',
                'name'       => $tokenName,
                'value'      => $accessToken,
            ],
        ];
    }
}
