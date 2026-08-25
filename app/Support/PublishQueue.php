<?php

namespace App\Support;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PublishQueue
{
    /** @return list<string> */
    public static function dailySlots(): array
    {
        $slots = config('publish_queue.daily_slots', []);

        return is_array($slots) ? array_values($slots) : [];
    }

    public static function postsPerDay(): int
    {
        return max(1, (int) config('publish_queue.posts_per_day', 7));
    }

    public static function timezone(): string
    {
        return (string) config('publish_queue.timezone', config('app.timezone', 'UTC'));
    }

    public static function isExcluded(Post $post): bool
    {
        $ids = config('publish_queue.excluded_ids', []);
        $slugs = config('publish_queue.excluded_slugs', []);

        if (is_array($ids) && in_array((int) $post->id, $ids, true)) {
            return true;
        }

        if (is_array($slugs) && in_array((string) $post->slug, $slugs, true)) {
            return true;
        }

        return false;
    }

    /**
     * Sonraki boş yayın slotunu hesapla (günde max N içerik).
     */
    public static function nextAvailableSlot(?Carbon $after = null): Carbon
    {
        $tz = self::timezone();
        $after ??= now($tz);
        $cursor = $after->copy()->timezone($tz);

        $slots = self::dailySlots();
        $limit = self::postsPerDay();

        if ($slots === []) {
            return $cursor->copy()->addDay()->startOfDay()->addHours(9);
        }

        for ($day = 0; $day < 730; $day++) {
            $date = $cursor->copy()->startOfDay()->addDays($day);
            $scheduledOnDay = self::scheduledTimesOnDate($date);

            foreach ($slots as $time) {
                [$hour, $minute] = array_map('intval', explode(':', $time));
                $candidate = $date->copy()->setTime($hour, $minute, 0);

                if ($candidate->lessThanOrEqualTo($after)) {
                    continue;
                }

                if ($scheduledOnDay->contains(fn (Carbon $existing) => $existing->equalTo($candidate))) {
                    continue;
                }

                if ($scheduledOnDay->count() >= $limit) {
                    break;
                }

                return $candidate;
            }
        }

        return $cursor->copy()->addDay()->startOfDay()->setTime(9, 0);
    }

    /**
     * Toplu kuyruk dağıtımı: DB'ye bakmadan sıradaki slot (mevcut yazıları taşırken).
     */
    public static function nextSequentialSlot(?Carbon $after = null): Carbon
    {
        $tz = self::timezone();
        $after ??= now($tz)->copy()->timezone($tz);
        $slots = self::dailySlots();
        $limit = self::postsPerDay();

        if ($slots === []) {
            return $after->copy()->addDay()->startOfDay()->addHours(9);
        }

        for ($day = 0; $day < 730; $day++) {
            $date = $after->copy()->timezone($tz)->startOfDay()->addDays($day);
            $count = 0;

            foreach ($slots as $time) {
                if ($count >= $limit) {
                    break;
                }

                [$hour, $minute] = array_map('intval', explode(':', $time));
                $candidate = $date->copy()->setTime($hour, $minute, 0);

                if ($candidate->greaterThan($after)) {
                    return $candidate;
                }

                $count++;
            }
        }

        return $after->copy()->addDay()->startOfDay()->setTime(9, 0);
    }

    public static function assignNextSlot(Post $post): Post
    {
        $slot = self::nextAvailableSlot();
        $post->status = Post::STATUS_SCHEDULED;
        $post->published_at = $slot;
        $post->queue_position = self::nextQueuePosition();

        return $post;
    }

    public static function nextQueuePosition(): int
    {
        $max = (int) Post::query()->max('queue_position');

        return $max + 1;
    }

    /**
     * @return Collection<int, Carbon>
     */
    private static function scheduledTimesOnDate(Carbon $date): Collection
    {
        $tz = self::timezone();
        $start = $date->copy()->timezone($tz)->startOfDay();
        $end = $date->copy()->timezone($tz)->endOfDay();

        return Post::query()
            ->where('status', Post::STATUS_SCHEDULED)
            ->whereBetween('published_at', [$start, $end])
            ->orderBy('published_at')
            ->pluck('published_at')
            ->map(fn ($value) => Carbon::parse($value)->timezone($tz));
    }

    /**
     * Form / admin girdisine göre yayın alanlarını ayarla.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function applyPublishingRules(array $data, ?Post $existing = null): array
    {
        $status = (string) ($data['status'] ?? Post::STATUS_SCHEDULED);
        $manualAt = trim((string) ($data['published_at'] ?? ''));
        unset($data['publish_now']);

        if ($status === Post::STATUS_DRAFT) {
            $data['status'] = Post::STATUS_DRAFT;
            $data['published_at'] = null;
            $data['queue_position'] = null;

            return $data;
        }

        if ($status === Post::STATUS_PUBLISHED) {
            $data['status'] = Post::STATUS_PUBLISHED;
            $data['published_at'] = $manualAt !== ''
                ? Carbon::parse($manualAt, self::timezone())
                : now(self::timezone());
            $data['queue_position'] = null;

            return $data;
        }

        // scheduled
        if ($manualAt !== '') {
            $data['status'] = Post::STATUS_SCHEDULED;
            $data['published_at'] = Carbon::parse($manualAt, self::timezone());
            $data['queue_position'] = $existing?->queue_position ?? self::nextQueuePosition();

            return $data;
        }

        // Otomatik kuyruk slotu
        $slot = self::nextAvailableSlot();
        $data['status'] = Post::STATUS_SCHEDULED;
        $data['published_at'] = $slot;
        $data['queue_position'] = $existing?->queue_position ?? self::nextQueuePosition();

        return $data;
    }

    public static function publishNow(Post $post): Post
    {
        $post->status = Post::STATUS_PUBLISHED;
        $post->published_at = now(self::timezone());
        $post->queue_position = null;
        $post->save();

        return $post;
    }
}
