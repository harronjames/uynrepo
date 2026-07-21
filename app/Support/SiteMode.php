<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class SiteMode
{
    public const LOCAL_URL = 'http://localhost:8000';

    public const PRODUCTION_URL = 'https://umzugland.at';

    public static function path(): string
    {
        return storage_path('app/site-mode.json');
    }

    public static function currentUrl(): string
    {
        $stored = self::read();

        if (! empty($stored['site_url'])) {
            return rtrim((string) $stored['site_url'], '/');
        }

        return rtrim((string) config('seo.site_url', self::PRODUCTION_URL), '/');
    }

    public static function currentMode(): string
    {
        $url = self::currentUrl();

        if (str_contains($url, 'localhost') || str_contains($url, '127.0.0.1')) {
            return 'local';
        }

        if (str_contains($url, 'umzugland.at')) {
            return 'production';
        }

        return 'custom';
    }

    public static function apply(string $mode): array
    {
        $mode = strtolower($mode);
        $url = $mode === 'local' ? self::LOCAL_URL : self::PRODUCTION_URL;

        File::ensureDirectoryExists(dirname(self::path()));
        File::put(self::path(), json_encode([
            'mode'     => $mode === 'local' ? 'local' : 'production',
            'site_url' => $url,
            'updated'  => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return [
            'mode'     => $mode === 'local' ? 'local' : 'production',
            'site_url' => $url,
        ];
    }

    public static function read(): array
    {
        if (! File::exists(self::path())) {
            return [];
        }

        $data = json_decode(File::get(self::path()), true);

        return is_array($data) ? $data : [];
    }

    public static function bootIntoConfig(): void
    {
        $url = self::currentUrl();

        config([
            'seo.site_url' => $url,
            'app.url'      => $url,
        ]);
    }
}
