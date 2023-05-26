<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\JsonApi\V1\Users\UserController;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\Relationships;
use LaravelJsonApi\Laravel\Routing\ActionRegistrar;
use Illuminate\Routing\Middleware\SubstituteBindings;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;

JsonApiRoute::server('v1')
    ->prefix('v1')
    ->withoutMiddleware(SubstituteBindings::class)
    ->resources(function (ResourceRegistrar $server) {
        $server->resource('users', UserController::class)
            ->only('store', 'show', 'update')
            ->relationships(function (Relationships $relations) {
                $relations->hasMany('posts')->readOnly();
            })
            ->actions('-actions', function (ActionRegistrar $actions) {
                $actions->post('login')->middleware('guest');
                $actions->post('logout')->middleware('auth:sanctum');
            });

        $server->resource('posts', JsonApiController::class)
            ->relationships(function (Relationships $relations) {
                $relations->hasOne('author')->readOnly();
                $relations->hasMany('comments')->readOnly();
                $relations->hasMany('tags');
            });
    });
