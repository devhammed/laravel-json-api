<?php

namespace App\JsonApi\V1\Posts;

use App\Models\Post;
use Illuminate\Http\Request;
use LaravelJsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use Illuminate\Database\Eloquent\Builder;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsToMany;

class PostSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     */
    public static string $model = Post::class;

    /**
     * The maximum include path depth.
     */
    protected int $maxDepth = 3;

    /**
     * The default pagination size.
     */
    protected ?array $defaultPagination = ['number' => 1, 'size' => 5];

    /**
     * Get the resource fields.
     */
    public function fields(): array
    {
        return [
            ID::make(),
            BelongsTo::make('author')->type('users')->readOnly(),
            HasMany::make('comments')->readOnly(),
            Str::make('title')->sortable(),
            Str::make('content'),
            Str::make('slug'),
            BelongsToMany::make('tags'),
            DateTime::make('created_at')->sortable()->readOnly(),
            DateTime::make('published_at')->sortable(),
            DateTime::make('updated_at')->sortable()->readOnly(),
        ];
    }

    /**
     * Get the resource filters.
     */
    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
        ];
    }

    /**
     * Get the resource paginator.
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }

    /**
     * Build an index query for this resource.
     */
    public function indexQuery(?Request $request, Builder $query): Builder
    {
        if ($user = $request?->user()) {
            return $query->where(function (Builder $q) use ($user) {
                return $q->whereNotNull('published_at')->orWhere('author_id', $user->getKey());
            });
        }

        return $query->whereNotNull('published_at');
    }
}
