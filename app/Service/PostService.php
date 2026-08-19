<?php

namespace App\Service;

use App\Models\Post;
use App\Support\HtmlSanitizer;
use App\Support\SchemaMarkup;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * TODO Abolish.
 *
 * @deprecated
 */
class PostService
{
    public function store($data)
    {
        try {
            DB::beginTransaction();

            $tagIds = null;
            if (isset($data['tag_ids'])) {
                $tagIds = $data['tag_ids'];
                unset($data['tag_ids']);
            }

            unset($data['remove_preview_image'], $data['remove_main_image']);

            $data = $this->normalizeContentFields($data);

            $categoryId = $data['category_id'] ?? null;
            unset($data['category_id']);

            $data['preview_image'] = $this->storeWebp($data['preview_image'] ?? null);
            $data['main_image'] = $this->storeWebp($data['main_image'] ?? null);

            // Drop empty image keys so nullable columns stay null.
            if ($data['preview_image'] === null) {
                unset($data['preview_image']);
            }
            if ($data['main_image'] === null) {
                unset($data['main_image']);
            }

            /** @phpstan-ignore-next-line */
            $post = Post::firstOrCreate($data);

            if ($categoryId) {
                $post->categories()->sync([(int) $categoryId]);
            }

            if ($tagIds) {
                $post->tags()->attach($tagIds);
            }
            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            abort(500);
        }
    }

    public function update($data, $post)
    {
        try {
            DB::beginTransaction();

            $tagIds = null;
            if (isset($data['tag_ids'])) {
                $tagIds = $data['tag_ids'];
                unset($data['tag_ids']);
            }

            $categoryId = $data['category_id'] ?? null;
            unset($data['category_id']);

            $data = $this->normalizeContentFields($data);

            $removePreview = (bool) ($data['remove_preview_image'] ?? false);
            $removeMain = (bool) ($data['remove_main_image'] ?? false);
            unset($data['remove_preview_image'], $data['remove_main_image']);

            if ($removePreview) {
                $this->deleteStoredFile($post->preview_image);
                $data['preview_image'] = null;
            } elseif (! empty($data['preview_image']) && $data['preview_image'] instanceof UploadedFile) {
                $this->deleteStoredFile($post->preview_image);
                $data['preview_image'] = $this->storeWebp($data['preview_image']);
            } else {
                unset($data['preview_image']);
            }

            if ($removeMain) {
                $this->deleteStoredFile($post->main_image);
                $data['main_image'] = null;
            } elseif (! empty($data['main_image']) && $data['main_image'] instanceof UploadedFile) {
                $this->deleteStoredFile($post->main_image);
                $data['main_image'] = $this->storeWebp($data['main_image']);
            } else {
                unset($data['main_image']);
            }

            // Force attribute assignment so null image removals are persisted.
            $post->fill($data);
            if (array_key_exists('preview_image', $data)) {
                $post->preview_image = $data['preview_image'];
            }
            if (array_key_exists('main_image', $data)) {
                $post->main_image = $data['main_image'];
            }
            $post->save();

            if ($categoryId) {
                $post->categories()->sync([(int) $categoryId]);
            }

            if ($tagIds !== null) {
                $post->tags()->sync($tagIds);
            }
            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            abort(500);
        }

        return $post;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeContentFields(array $data): array
    {
        if (array_key_exists('content', $data)) {
            $data['content'] = HtmlSanitizer::clean((string) $data['content']);
        }

        if (array_key_exists('schema_json', $data)) {
            $json = trim((string) $data['schema_json']);
            $data['schema_json'] = $json === '' ? null : SchemaMarkup::toSafeScript($json);
        }

        foreach (['meta_title', 'meta_description', 'meta_keywords'] as $field) {
            if (array_key_exists($field, $data)) {
                $value = trim(strip_tags((string) $data[$field]));
                $data[$field] = $value === '' ? null : $value;
            }
        }

        return $data;
    }

    private function storeWebp(mixed $file): ?string
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $mime = strtolower((string) $file->getMimeType());

        if ($extension !== 'webp' || ! in_array($mime, ['image/webp', 'image/x-webp'], true)) {
            return null;
        }

        $path = $file->store('images', 'public');

        return $path ? '/storage/' . ltrim($path, '/') : null;
    }

    private function deleteStoredFile(?string $url): void
    {
        if (! $url) {
            return;
        }

        $path = $url;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $path = (string) (parse_url($path, PHP_URL_PATH) ?? '');
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if ($path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
