<?php

namespace App\JsonApi\V1;

use Illuminate\Auth\AuthManager;
use LaravelJsonApi\Core\Server\Server as BaseServer;

class Server extends BaseServer
{
    /**
     * The base URI namespace for this server.
     */
    protected string $baseUri = '/api/v1';

    /**
     * Bootstrap the server when it is handling an HTTP request.
     */
    public function serving(AuthManager $auth): void
    {
        $auth->shouldUse('sanctum');
    }

    /**
     * Get the server's list of schemas.
     */
    protected function allSchemas(): array
    {
        return [
            Users\UserSchema::class,
            Posts\PostSchema::class,
            Tags\TagSchema::class,
            Comments\CommentSchema::class,
        ];
    }
}
