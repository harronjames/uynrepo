<?php

namespace App\Rules;

use App\Support\SchemaMarkup;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidJsonLd implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! is_string($value)) {
            $fail('Das Schema-Feld muss gültiges JSON-LD enthalten.');

            return;
        }

        if (strlen($value) > SchemaMarkup::MAX_BYTES) {
            $fail('Das JSON-LD Schema ist zu groß (max. 32 KB).');

            return;
        }

        if (! SchemaMarkup::isValid($value)) {
            $fail('Ungültiges JSON-LD. Bitte ein JSON-Objekt mit @type, @graph oder @context einfügen.');
        }
    }
}
