<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            // BIGINT unsigned agar cocok dengan categories.id.
            $table->unsignedBigInteger('category_id');

            $table->string('name', 200);
            $table->string('slug', 200);
            $table->text('description')->nullable();
            $table->decimal('rental_price_per_day', 12, 2);
            $table->decimal('replacement_cost', 12, 2)->default(0);
            $table->decimal('deposit_fee', 12, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Slug hanya harus unik di dalam tenant yang sama.
            $table->unique(['tenant_id', 'id']);
            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'category_id', 'is_available']);

            // Memastikan kategori produk dimiliki tenant yang sama.
            $table->foreign(['tenant_id', 'category_id'])
                ->references(['tenant_id', 'id'])
                ->on('categories')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};