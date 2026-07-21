<?php

namespace App\Http\Requests\Admin\Page;

use App\Support\WebpImage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateImpressumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:255',
            'image'        => WebpImage::RULE,
            'remove_image' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'image.mimes'     => 'Nur WebP-Dateien sind erlaubt.',
            'image.mimetypes' => 'Nur WebP-Dateien sind erlaubt.',
        ];
    }
}
