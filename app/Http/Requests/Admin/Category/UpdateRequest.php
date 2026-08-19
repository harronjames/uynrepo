<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $allowed = strtolower((string) config('auth.admin_email'));

        return $user !== null
            && $user->isAdministrator()
            && strtolower((string) $user->email) === $allowed;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'title'            => 'required|string',
            'meta_title'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:10000',
            'meta_keywords'    => 'nullable|string|max:255',
        ];
    }
}
