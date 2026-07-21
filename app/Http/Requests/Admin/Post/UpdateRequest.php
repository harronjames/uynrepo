<?php

namespace App\Http\Requests\Admin\Post;

use App\Support\WebpImage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                => 'required|string',
            'content'              => 'required|string',
            'meta_title'           => 'nullable|string|max:70',
            'meta_description'     => 'nullable|string|max:160',
            'meta_keywords'        => 'nullable|string|max:255',
            'category_id'          => 'nullable|integer|exists:categories,id',
            'preview_image'        => WebpImage::RULE,
            'main_image'           => WebpImage::RULE,
            'remove_preview_image' => 'nullable|boolean',
            'remove_main_image'    => 'nullable|boolean',
            'tag_ids'              => 'nullable|array',
            'tag_ids.*'            => 'nullable|integer|exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'preview_image.mimes'     => 'Nur WebP-Dateien sind erlaubt.',
            'preview_image.mimetypes' => 'Nur WebP-Dateien sind erlaubt.',
            'main_image.mimes'        => 'Nur WebP-Dateien sind erlaubt.',
            'main_image.mimetypes'    => 'Nur WebP-Dateien sind erlaubt.',
        ];
    }
}
