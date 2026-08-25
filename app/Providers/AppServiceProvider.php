<?php

namespace App\Providers;

use App\Models\Post;
use App\View\Composers\PortalSeoComposer;
use App\View\Composers\PublicLayoutComposer;
use App\Support\SiteMode;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SiteMode::bootIntoConfig();

        if ($siteUrl = config('seo.site_url')) {
            URL::forceRootUrl($siteUrl);

            $scheme = parse_url($siteUrl, PHP_URL_SCHEME);
            if (is_string($scheme) && $scheme !== '') {
                URL::forceScheme($scheme);
            }
        }

        Paginator::useBootstrap();
        Carbon::setLocale('de');
        app()->setLocale('de');

        View::composer([
            'layouts.portal',
            'layouts.with-sidebar',
            'layouts.wrapper',
            'layouts.wrapper._footer',
        ], PublicLayoutComposer::class);

        View::composer([
            'layouts.with-sidebar',
        ], PortalSeoComposer::class);

        Route::bind('post', function (string $value) {
            $query = Post::query();

            if (ctype_digit($value)) {
                $query->where('id', (int) $value);
            } else {
                $query->where('slug', $value);
            }

            $user = auth()->user();
            if (! $user || ! $user->isSiteAdmin()) {
                $query->publiclyVisible();
            }

            return $query->firstOrFail();
        });
    }
}
