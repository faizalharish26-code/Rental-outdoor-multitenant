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
        Schema::create('orders', function (Blueprint $table): void {
            $table->id(); // Menggunakan BIGINT sesuai baseline proyek
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete(); // Isolasi multi-tenant[cite: 1]
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete(); // Relasi ke tabel users
            
            $table->string('order_code', 50)->unique(); // Kode order unik (misal: RENT-YYMMDD-XXXX)
            
            $table->date('start_date');
            $table->date('end_date');
            
            $table->decimal('total_amount', 12, 2); // Format uang DECIMAL(12,2)[cite: 1]
            $table->decimal('deposit_amount', 12, 2); // Sesuai aturan DP 50% SRS[cite: 1]
            
            $table->string('shipping_method', 50);
            $table->text('shipping_address')->nullable();
            $table->decimal('shipping_cost', 12, 2)->default(0);
            
            $table->string('status', 50)->default('pending'); // pending, confirmed, returned, cancelled
            $table->string('payment_status', 50)->default('belum_bayar'); // belum_bayar, dp_lunas, lunas, dll
            
            $table->string('tripay_reference', 255)->nullable()->unique();
            $table->string('idempotency_key', 100)->unique(); // Mencegah duplikasi booking
            
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            // Index untuk performa query tenant & customer
            $table->index(['tenant_id', 'status']);
            $table->index(['customer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};