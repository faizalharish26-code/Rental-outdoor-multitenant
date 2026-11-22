<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_units', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('product_id');
            $table->string('unit_code', 100);
            $table->string('condition', 50)->default('good');
            $table->string('status', 30)->default('available');
            $table->text('maintenance_note')->nullable();
            $table->date('maintenance_started_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'unit_code']);
            $table->index(['tenant_id', 'status']);

            $table->foreign(['tenant_id', 'product_id'])
                ->references(['tenant_id', 'id'])
                ->on('products')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_units');
    }
};