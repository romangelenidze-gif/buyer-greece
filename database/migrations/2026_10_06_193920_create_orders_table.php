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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('public_order_number')->unique(); // e.g. ORD-881293
        $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
        $table->string('type'); // buy_for_me, manual_order
        $table->string('status'); // OrderStatus Enum
        $table->unsignedBigInteger('active_quote_id')->nullable();
        $table->text('internal_note')->nullable();
        $table->timestamp('submitted_at');
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();
        $table->softDeletes();
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
