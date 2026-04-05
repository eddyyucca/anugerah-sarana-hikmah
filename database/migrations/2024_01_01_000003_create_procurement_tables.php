<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Purchase Requests
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pr_number', 30)->unique();
            $table->date('request_date');
            $table->unsignedBigInteger('request_by')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('draft'); // draft, submitted, approved, rejected, closed
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('request_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->index('status');
            $table->index('request_date');
            $table->index(['status', 'request_date']);
        });

        // Purchase Request Items
        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_request_id');
            $table->unsignedBigInteger('sparepart_id');
            $table->integer('qty')->default(1);
            $table->string('notes', 255)->nullable();

            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->cascadeOnDelete();
            $table->foreign('sparepart_id')->references('id')->on('spareparts');
            $table->index(['purchase_request_id', 'sparepart_id']);
        });

        // Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 30)->unique();
            $table->unsignedBigInteger('purchase_request_id')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->date('po_date');
            $table->date('expected_date')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('draft'); // draft, issued, partial, completed, cancelled
            $table->timestamps();

            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->nullOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->index('status');
            $table->index('po_date');
            $table->index(['supplier_id', 'po_date']);
            $table->index(['status', 'po_date']);
        });

        // Purchase Order Items
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('sparepart_id');
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('total_price', 18, 2)->default(0);

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete();
            $table->foreign('sparepart_id')->references('id')->on('spareparts');
            $table->index(['purchase_order_id', 'sparepart_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_request_items');
        Schema::dropIfExists('purchase_requests');
    }
};
