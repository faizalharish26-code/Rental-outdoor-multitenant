<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_fulfillments', function (Blueprint $table): void {
            $table->id(); // Menggunakan BIGINT sesuai baseline proyek
            
            // Relasi tenant, order item, dan unit inventori fisik
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_unit_id')->nullable();
            
            // Catatan kondisi saat checkout (penyerahan) dan checkin (pengembalian)
            $table->text('checkout_condition_notes')->nullable();
            $table->text('checkin_condition_notes')->nullable();
            
            // Denda (menggunakan tipe DECIMAL(12,2) sesuai aturan uang)
            $table->decimal('penalty_amount', 12, 2)->default(0.00); 
            
            // Status fulfillment
            $table->string('status', 50)->default('assigned'); // contoh: assigned, checked_out, returned, damaged, lost
            
            // Timestamp proses serah-terima
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            
            $table->timestamps();

            // Index tambahan untuk performa query tenant & status
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_fulfillments');
    }
};