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
        Schema::create('incoming_packages', function (Blueprint $table) {
            $table->id();
            $table->string('public_package_number')->unique(); // e.g. PKG-0001
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('restrict');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('restrict');
            $table->foreignId('shipment_id')->nullable()->constrained('shipments')->onDelete('set null');
            $table->string('source_type'); // buyer_order, self_purchase, other
            $table->string('store_name')->nullable();
            $table->string('store_tracking_number')->nullable();
            $table->text('description')->nullable();
            $table->date('expected_date')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->decimal('weight_kg', 8, 3)->nullable();
            $table->string('dimensions')->nullable();
            $table->string('status'); // expected, received_in_greece, ready_for_shipment, assigned_to_shipment, unidentified, problem
            $table->text('photos_path')->nullable();
            $table->text('internal_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_packages');
    }
};
