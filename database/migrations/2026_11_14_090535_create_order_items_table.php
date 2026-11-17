<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id(); // Menggunakan BIGINT sesuai baseline proyek
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete(); // Isolasi multi-tenant[cite: 1]
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('inventory_unit_id')->nullable();
            
            $table->integer('quantity');
            $table->decimal('price_per_day', 12, 2); // Format uang DECIMAL(12,2)[cite: 1]
            $table->integer('duration_days')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->string('product_name_snapshot', 200)->nullable(); // Snapshot nama produk agar riwayat aman
            
            $table->timestamps();

            // Index untuk performa query tenant & produk
            $table->index(['tenant_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};