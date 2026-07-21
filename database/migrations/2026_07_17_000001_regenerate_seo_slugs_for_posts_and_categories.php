<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Post::query()->each(function (Post $post): void {
            $post->slug = Post::generateUniqueSlug($post->title, $post->id);
            $post->saveQuietly();
        });

        Category::query()->each(function (Category $category): void {
            $category->slug = Category::generateUniqueSlug($category->title, $category->id);
            $category->saveQuietly();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
