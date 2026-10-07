<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('source_type')->default('self_purchase');
            $table->string('status')->default('expected');
            
            $table->string('incoming_tracking_number')->nullable()->index();
            $table->string('store_name')->nullable();
            $table->text('description')->nullable();
            
            $table->decimal('weight_kg', 8, 2)->default(0);
            $table->string('dimensions')->nullable();
            $table->decimal('declared_value', 10, 2)->default(0);
            
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};