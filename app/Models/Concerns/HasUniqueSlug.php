<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUniqueSlug
{
    public static function bootHasUniqueSlug(): void
    {
        static::saving(function (Model $model): void {
            if (empty($model->slug) || $model->isDirty('title')) {
                $model->slug = static::generateUniqueSlug(
                    (string) $model->title,
                    $model->exists ? (int) $model->getKey() : null
                );
            }
        });
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);

        if ($base === '') {
            $base = 'eintrag';
        }

        $base = Str::limit($base, 120, '');
        $slug = $base;
        $counter = 1;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $suffix = '-' . $counter;
            $slug   = Str::limit($base, 120 - strlen($suffix), '') . $suffix;
            $counter++;
        }

        return $slug;
    }
}
