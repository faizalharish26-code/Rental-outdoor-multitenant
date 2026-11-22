<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('deposit_amount', 12, 2)->default(0)->after('total_amount');
            $table->string('payment_status', 20)->default('unpaid')->after('status')->index();
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->string('provider', 30);
            $table->string('reference', 80)->unique();
            $table->decimal('amount', 12, 2);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex(['payment_status']);
            $table->dropColumn(['deposit_amount', 'payment_status']);
        });
    }
};
