<?php

namespace App\Http\Requests\Admin\Post;

use App\Rules\ValidJsonLd;
use App\Support\WebpImage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $allowed = strtolower((string) config('auth.admin_email'));

        return $user !== null
            && $user->isAdministrator()
            && strtolower((string) $user->email) === $allowed;
    }

    protected function prepareForValidation(): void
    {
        // HTML checkboxes are absent when unchecked; normalize to real booleans.
        $this->merge([
            'remove_preview_image' => $this->boolean('remove_preview_image'),
            'remove_main_image'    => $this->boolean('remove_main_image'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title'                => 'required|string|max:180',
            'content'              => 'required|string',
            'meta_title'           => 'nullable|string|max:70',
            'meta_description'     => 'nullable|string|max:160',
            'meta_keywords'        => 'nullable|string|max:255',
            'schema_json'          => ['nullable', 'string', 'max:32000', new ValidJsonLd()],
            'category_id'          => 'nullable|integer|exists:categories,id',
            'preview_image'        => WebpImage::RULE,
            'main_image'           => WebpImage::RULE,
            'remove_preview_image' => 'sometimes|boolean',
            'remove_main_image'    => 'sometimes|boolean',
            'tag_ids'              => 'nullable|array',
            'tag_ids.*'            => 'nullable|integer|exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'preview_image.extensions' => 'Nur WebP-Dateien sind erlaubt.',
            'preview_image.mimes'      => 'Nur WebP-Dateien sind erlaubt.',
            'preview_image.mimetypes'  => 'Nur WebP-Dateien sind erlaubt.',
            'main_image.extensions'    => 'Nur WebP-Dateien sind erlaubt.',
            'main_image.mimes'         => 'Nur WebP-Dateien sind erlaubt.',
            'main_image.mimetypes'     => 'Nur WebP-Dateien sind erlaubt.',
        ];
    }
}
