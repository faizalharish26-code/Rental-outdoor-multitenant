<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tenant = $this->route('tenant');

        if (! $tenant instanceof Tenant) {
            return false;
        }

        $product = $this->route('product');

        if ($product instanceof Product) {
            return $this->user()?->can('update', $product) === true;
        }

        return $this->user()?->can('create', [Product::class, $tenant]) === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug((string) $this->input('slug')),
        ]);
    }

    public function rules(): array
    {
        /** @var Tenant $tenant */
        $tenant = $this->route('tenant');
        $product = $this->route('product');

        return [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where('tenant_id', $tenant->id),
            ],
            'name' => ['required', 'string', 'max:200'],
            'slug' => [
                'required',
                'alpha_dash',
                'max:200',
                Rule::unique('products', 'slug')
                    ->where('tenant_id', $tenant->id)
                    ->ignore($product?->id),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'rental_price_per_day' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
            'replacement_cost' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
            'deposit_fee' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
            'is_available' => ['required', 'boolean'],
        ];
    }
}