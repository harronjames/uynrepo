<!doctype html>
<html lang="de-AT" data-bs-theme="light">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">
        <title>{{ $seo['title'] ?? $title ?? 'Umzugland.at' }}</title>

        @php
            $metaDescription = $seo['description'] ?? null;
            $metaKeywords = $seo['keywords'] ?? null;
            $canonicalUrl = $seo['canonical'] ?? url()->current();
            $ogType = $seo['type'] ?? 'website';
            $ogImage = $seo['image'] ?? null;
        @endphp

        @if (!empty($metaDescription))
                <meta name="description" content="{{ $metaDescription }}">
        @endif

        @if (!empty($metaKeywords))
                <meta name="keywords" content="{{ $metaKeywords }}">
        @endif

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="canonical" href="{{ $canonicalUrl }}">
        <link rel="alternate" hreflang="de-AT" href="{{ $canonicalUrl }}">
        <link rel="alternate" hreflang="x-default" href="{{ $canonicalUrl }}">

        <meta property="og:locale" content="de_AT">
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:site_name" content="Umzugland.at">
        <meta property="og:title" content="{{ $seo['title'] ?? $title ?? 'Umzugland.at' }}">
        @if (!empty($metaDescription))
                <meta property="og:description" content="{{ $metaDescription }}">
        @endif
        <meta property="og:url" content="{{ $canonicalUrl }}">
        @if (!empty($ogImage))
                <meta property="og:image" content="{{ $ogImage }}">
        @endif
        @if (($ogType ?? '') === 'article')
                @if (!empty($seo['published_time']))
                        <meta property="article:published_time" content="{{ $seo['published_time'] }}">
                @endif
                @if (!empty($seo['modified_time']))
                        <meta property="article:modified_time" content="{{ $seo['modified_time'] }}">
                @endif
        @endif

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seo['title'] ?? $title ?? 'Umzugland.at' }}">
        @if (!empty($metaDescription))
                <meta name="twitter:description" content="{{ $metaDescription }}">
        @endif
        @if (!empty($ogImage))
                <meta name="twitter:image" content="{{ $ogImage }}">
        @endif

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/assets/css/app.css">

        @stack('styles')
        @include('layouts.wrapper._structured-data')
</head>

<body class="d-flex flex-column min-vh-100 portal-body">

        @include('layouts.wrapper._navbar')

        <main class="flex-grow-1">
                @yield('content')
        </main>

        @include('layouts.wrapper._footer')

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
                crossorigin="anonymous"></script>

        @stack('scripts')
</body>

</html>
