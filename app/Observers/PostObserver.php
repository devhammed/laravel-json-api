<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Auth\AuthManager;

class PostObserver
{
    /**
     * Construct the PostObserver.
     */
    public function __construct(protected AuthManager $auth)
    {
    }

    /**
     * Handle the Post "creating" event.
     */
    public function creating(Post $post): void
    {
        // Associate the authenticated user with the post.
        if (!$post->author_id) {
            $post->author()->associate($this->auth->user());
        }

        // Generate the post's slug.
        if (!$post->slug) {
            $post->slug = $post->generateSlug();
        }
    }

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
