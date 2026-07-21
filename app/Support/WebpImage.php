<?php

namespace App\Support;

class WebpImage
{
    public const RULE = 'nullable|file|mimes:webp|mimetypes:image/webp|max:5120';

    public const ACCEPT = '.webp,image/webp';
}
