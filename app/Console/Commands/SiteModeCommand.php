<?php

namespace App\Console\Commands;

use App\Support\SiteMode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class SiteModeCommand extends Command
{
    protected $signature = 'site:mode {mode? : local|production|status}';

    protected $description = 'URL modunu göster veya değiştir (local = localhost:8000, production = umzugland.at)';

    public function handle(): int
    {
        $mode = strtolower((string) ($this->argument('mode') ?: 'status'));

        return match ($mode) {
            'status', '' => $this->showStatus(),
            'local', 'dev', 'localhost' => $this->switchTo('local'),
            'production', 'prod', 'live' => $this->switchTo('production'),
            default => $this->invalidMode($mode),
        };
    }

    private function showStatus(): int
    {
        $this->line('Aktif URL modu: <info>' . SiteMode::currentMode() . '</info>');
        $this->line('Aktif site URL: <comment>' . SiteMode::currentUrl() . '</comment>');
        $this->newLine();
        $this->line('Geçiş:');
        $this->line('  php artisan site:mode local');
        $this->line('  php artisan site:mode production');

        return self::SUCCESS;
    }

    private function switchTo(string $mode): int
    {
        $result = SiteMode::apply($mode);

        foreach ([base_path('.env'), base_path('.env.local')] as $path) {
            if (! File::exists($path)) {
                continue;
            }

            $this->upsertEnvValue($path, 'SEO_SITE_URL', $result['site_url']);
            $this->upsertEnvValue($path, 'APP_URL', $result['site_url']);
        }

        SiteMode::bootIntoConfig();
        URL::forceRootUrl($result['site_url']);
        $scheme = parse_url($result['site_url'], PHP_URL_SCHEME);
        if (is_string($scheme) && $scheme !== '') {
            URL::forceScheme($scheme);
        }

        Artisan::call('config:clear');

        $this->info('Mod: ' . $result['mode']);
        $this->line('URL: ' . $result['site_url']);
        $this->line('Örnek route: ' . route('main.index'));

        return self::SUCCESS;
    }

    private function upsertEnvValue(string $path, string $key, string $value): void
    {
        $contents = File::get($path);
        $line = $key . '=' . $value;

        if (preg_match('/^' . preg_quote($key, '/') . '=.*$/m', $contents)) {
            $contents = preg_replace('/^' . preg_quote($key, '/') . '=.*$/m', $line, $contents);
        } else {
            $contents = rtrim($contents) . PHP_EOL . $line . PHP_EOL;
        }

        File::put($path, $contents);
    }

    private function invalidMode(string $mode): int
    {
        $this->error("Bilinmeyen mod: {$mode}");
        $this->line('Kullanım: php artisan site:mode [status|local|production]');

        return self::FAILURE;
    }
}
