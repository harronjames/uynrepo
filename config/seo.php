<?php

return [
    'site_name' => env('SEO_SITE_NAME', 'Umzugland.at'),
    // Canonical, schema, sitemap and route() absolute URLs always use the public domain.
    'site_url'  => rtrim(env('SEO_SITE_URL', 'https://umzugland.at'), '/'),

    'organization' => [
        'name'              => 'Umzugland',
        'owner'             => 'Mesut Duman',
        'email'             => 'office@umzugland.at',
        'telephone'         => '+4369912526012',
        'telephone_display' => '+43 699 12526012',
        'address'           => [
            'street'      => 'Heigerleinstraße 23',
            'postal_code' => '1160',
            'locality'    => 'Wien',
            'country'     => 'AT',
        ],
        'description' => 'Unabhängiger Ratgeber für Umzug, Räumung, Entrümpelung und Leben in Wien und Österreich. Rein informativ – ohne Verkauf oder Dienstleistungsangebot.',
        'locale'      => 'de-AT',
    ],
];
