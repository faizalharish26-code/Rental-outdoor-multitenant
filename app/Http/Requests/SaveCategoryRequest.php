<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tenant = $this->route('tenant');

        if (! $tenant instanceof Tenant) {
            return false;
        }

        $category = $this->route('category');

        if ($category instanceof Category) {
            if ((int) $category->tenant_id !== (int) $tenant->id) {
                return false;
            }

            return $this->user()?->can('update', $category) === true;
        }

        return $this->user()?->can('create', [Category::class, $tenant]) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Tenant $tenant */
        $tenant = $this->route('tenant');

        $category = $this->route('category');
        $ignoredCategoryId = $category instanceof Category
            ? $category->getKey()
            : null;

        return [
            'name' => ['required', 'string', 'max:200'],
            'slug' => [
                'required',
                'string',
                'max:200',
                'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/',
                Rule::unique('categories', 'slug')
                    ->where('tenant_id', $tenant->id)
                    ->ignore($ignoredCategoryId),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 200 karakter.',
            'slug.required' => 'Slug kategori wajib diisi.',
            'slug.string' => 'Slug kategori harus berupa teks.',
            'slug.max' => 'Slug kategori maksimal 200 karakter.',
            'slug.regex' => 'Gunakan huruf kecil, angka, dan tanda hubung, misalnya alat-masak.',
            'slug.unique' => 'Slug ini sudah digunakan oleh kategori lain di tenant ini.',
        ];
    }
}
