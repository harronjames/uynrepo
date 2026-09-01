<?php

return [

    'enabled' => (bool) env('INTERNAL_LINKS_ENABLED', true),

    'max_links_per_post' => (int) env('INTERNAL_LINKS_MAX_PER_POST', 6),

    'link_class' => 'portal-inline-link',

    /*
    |--------------------------------------------------------------------------
    | Kontextuelle Phrasen → Beitrag oder Kategorie (zur Laufzeit per Titel)
    |--------------------------------------------------------------------------
    |
    | Längere Phrasen zuerst matchen (automatisch nach Textlänge sortiert).
    | post_title_contains / category_title_contains: Teilstring im DB-Titel.
    |
    */
    'phrases' => [
        ['text' => 'Entrümpelung in Wien', 'post_title_contains' => 'Entrümpelung in Wien'],
        ['text' => 'Umzug in Wien', 'post_title_contains' => 'Umzug in Wien Checkliste'],
        ['text' => 'Halteverbotszone', 'post_title_contains' => 'Halteverbotszone'],
        ['text' => 'Wohnungsübergabe', 'post_title_contains' => 'Wohnungsübergabe'],
        ['text' => 'Sperrmüllentsorgung', 'post_title_contains' => 'Sperrmüllentsorgung'],
        ['text' => 'Möbeltransport', 'post_title_contains' => 'Möbeltransport'],
        ['text' => 'Küche umziehen', 'post_title_contains' => 'Küche umziehen'],
        ['text' => 'Entrümpelung', 'category_title_contains' => 'Entrümpelung'],
        ['text' => 'Sperrmüll', 'post_title_contains' => 'Sperrmüll'],
        ['text' => 'Umzug Wien', 'category_title_contains' => 'Umzug Wien'],
        ['text' => 'Checkliste', 'post_title_contains' => 'Checkliste'],
    ],

    /*
    | Skip linking inside these ancestor tags (article headings stay clean).
    */
    'skip_ancestor_tags' => ['h1', 'h2', 'h3', 'a', 'code', 'pre', 'figcaption'],

];
