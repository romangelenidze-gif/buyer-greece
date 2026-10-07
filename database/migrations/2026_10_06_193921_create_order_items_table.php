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
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
        $table->text('product_url')->nullable();
        $table->string('product_name');
        $table->unsignedInteger('quantity')->default(1);
        $table->string('size')->nullable();
        $table->string('color')->nullable();
        $table->string('model')->nullable();
        $table->text('options')->nullable();
        $table->text('requested_comment')->nullable();
        $table->decimal('unit_price', 10, 2)->nullable();
        $table->string('currency', 3)->default('EUR');
        $table->text('admin_comment')->nullable();
        $table->timestamps();
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
