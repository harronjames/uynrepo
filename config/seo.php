<?php

return [
    'site_name' => env('SEO_SITE_NAME', 'Umzugland.at'),
    // Canonical, schema, sitemap and route() absolute URLs always use the public domain.
    'site_url'  => rtrim(env('SEO_SITE_URL', 'https://umzugland.at'), '/'),

    'organization' => [
        'name'        => 'Umzugland.at',
        'email'       => 'info@umzugland.at',
        'description' => 'Ratgeber für Umzug, Räumung, Entrümpelung und Leben in Wien und Österreich.',
        'locale'      => 'de-AT',
    ],
];
