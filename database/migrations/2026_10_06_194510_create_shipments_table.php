<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('public_shipment_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('status')->default('preparing');
            $table->string('carrier')->default('CAMEX');
            $table->string('destination_country')->nullable();
            
            $table->decimal('weight_kg', 8, 2)->default(0);
            $table->string('camex_tracking_number')->nullable();
            $table->string('camex_status')->nullable();
            
            $table->timestamp('transferred_to_camex_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('internal_note')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};