<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPostsCommand extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Zamanı gelen scheduled yazıları otomatik published yap';

    public function handle(): int
    {
        $posts = Post::query()
            ->scheduled()
            ->where('published_at', '<=', now())
            ->orderBy('published_at')
            ->get();

        if ($posts->isEmpty()) {
            $this->line('Yayınlanacak zamanlanmış yazı yok.');

            return self::SUCCESS;
        }

        foreach ($posts as $post) {
            $post->status = Post::STATUS_PUBLISHED;
            $post->queue_position = null;
            $post->save();

            $this->line("Published #{$post->id} {$post->title}");
        }

        $this->info('Toplam: ' . $posts->count());

        return self::SUCCESS;
    }
}
