<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hariç tutulan yazılar (hemen yayında kalır, kuyruğa alınmaz)
    |--------------------------------------------------------------------------
    |
    | posts:queue-existing komutu bu ID ve slug listesindeki yazılara dokunmaz.
    | Sadece Post kayıtları etkilenir.
    |
    */
    'excluded_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('PUBLISH_QUEUE_EXCLUDED_IDS', ''))
    ))),

    'excluded_slugs' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('PUBLISH_QUEUE_EXCLUDED_SLUGS', ''))
    ))),

    'posts_per_day' => (int) env('PUBLISH_QUEUE_POSTS_PER_DAY', 7),

    'daily_slots' => [
        '09:00',
        '11:30',
        '14:00',
        '16:30',
        '18:30',
        '20:30',
        '22:30',
    ],

    'timezone' => env('PUBLISH_QUEUE_TIMEZONE', env('APP_TIMEZONE', 'Europe/Vienna')),

];
