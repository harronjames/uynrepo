<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Support\PublishQueue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueueExistingPostsCommand extends Command
{
    protected $signature = 'posts:queue-existing
                            {--dry-run : Değişiklikleri göster, kaydetme}
                            {--force : Hariç listesindekiler dışındaki tüm yazıları yeniden kuyruğa al}';

    protected $description = 'Mevcut yazıları drip-feed kuyruğuna dağıt (hariç listesi config/publish_queue.php)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $excludedIds = config('publish_queue.excluded_ids', []);
        $excludedSlugs = config('publish_queue.excluded_slugs', []);

        $this->info('Hariç tutulan ID: ' . (is_array($excludedIds) && $excludedIds !== [] ? implode(', ', $excludedIds) : '(yok)'));
        $this->info('Hariç tutulan slug: ' . (is_array($excludedSlugs) && $excludedSlugs !== [] ? implode(', ', $excludedSlugs) : '(yok)'));
        $this->newLine();

        if ($dryRun) {
            $this->warn('Dry-run modu – veritabanına yazılmayacak.');
        }

        $posts = Post::query()->orderBy('id')->get();
        $queued = 0;
        $kept = 0;
        $skipped = 0;

        /** @var Carbon $cursor */
        $cursor = now(PublishQueue::timezone());

        DB::beginTransaction();

        try {
            foreach ($posts as $post) {
                if (PublishQueue::isExcluded($post)) {
                    if ($force || $post->status !== Post::STATUS_PUBLISHED) {
                        $post->status = Post::STATUS_PUBLISHED;
                        $post->published_at = $post->published_at ?? $post->created_at ?? now();
                        $post->queue_position = null;
                        if (! $dryRun) {
                            $post->save();
                        }
                        $this->line("  KEEP  #{$post->id} {$post->title} → published");
                    } else {
                        $this->line("  SKIP  #{$post->id} {$post->title} (hariç, zaten yayında)");
                    }
                    $kept++;
                    continue;
                }

                if (! $force && $post->status === Post::STATUS_SCHEDULED && $post->published_at?->isFuture()) {
                    $this->line("  SKIP  #{$post->id} {$post->title} (zaten kuyrukta)");
                    $skipped++;
                    continue;
                }

                $slot = PublishQueue::nextSequentialSlot($cursor);
                $cursor = $slot->copy();

                $post->status = Post::STATUS_SCHEDULED;
                $post->published_at = $slot;
                $post->queue_position = $queued + 1;

                if (! $dryRun) {
                    $post->save();
                }

                $this->line(sprintf(
                    '  QUEUE #%d %s → %s',
                    $post->id,
                    $post->title,
                    $slot->timezone(PublishQueue::timezone())->format('d.m.Y H:i')
                ));

                $queued++;
            }

            if (! $dryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info("Kuyruğa alındı: {$queued}");
        $this->info("Hariç / yayında kaldı: {$kept}");
        $this->info("Atlandı: {$skipped}");

        return self::SUCCESS;
    }
}
