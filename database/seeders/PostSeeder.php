<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(5)->createQuietly();

        Post::factory()->count(100)->createQuietly([
            'author_id' => fn () => $users->random(),
        ]);
    }
}
