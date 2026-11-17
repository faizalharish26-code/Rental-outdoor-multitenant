<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Publik (Guest & User terautentikasi boleh akses)
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'min:1'],
        ];
    }
}