<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSeoMeta
{
    public function seoTitle(): string
    {
        $metaTitle = trim((string) ($this->meta_title ?? ''));

        return $metaTitle !== '' ? $metaTitle : (string) $this->title;
    }

    public function seoDescription(int $words = 25): string
    {
        $metaDescription = trim((string) ($this->meta_description ?? ''));

        if ($metaDescription !== '') {
            return $metaDescription;
        }

        if (method_exists($this, 'shortBody')) {
            return $this->shortBody($words);
        }

        return (string) $this->title;
    }

    public function seoKeywords(): ?string
    {
        $keywords = trim((string) ($this->meta_keywords ?? ''));

        return $keywords !== '' ? $keywords : null;
    }

    public function toSeoPayload(?string $canonical = null): array
    {
        return [
            'title'       => $this->seoTitle(),
            'description' => Str::limit($this->seoDescription(), 160, ''),
            'keywords'    => $this->seoKeywords(),
            'canonical'   => $canonical,
        ];
    }
}
