<?php

namespace App\Support;

class AbsoluteUrl
{
    public static function from(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $path = trim($path);

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $base = rtrim((string) config('seo.site_url', 'https://umzugland.at'), '/');

        return $base . '/' . ltrim($path, '/');
    }
}
