<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Goods Receipts
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('gr_number', 30)->unique();
            $table->unsignedBigInteger('purchase_order_id');
            $table->date('receipt_date');
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('draft'); // draft, posted, cancelled
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();
            $table->index('status');
            $table->index('receipt_date');
            $table->index(['status', 'receipt_date']);
        });

        // Goods Receipt Items
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_receipt_id');
            $table->unsignedBigInteger('sparepart_id');
            $table->unsignedBigInteger('warehouse_location_id')->nullable();
            $table->integer('qty_received')->default(0);

            $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->cascadeOnDelete();
            $table->foreign('sparepart_id')->references('id')->on('spareparts');
            $table->foreign('warehouse_location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->index(['goods_receipt_id', 'sparepart_id']);
        });

        // Goods Issues
        Schema::create('goods_issues', function (Blueprint $table) {
            $table->id();
            $table->string('gi_number', 30)->unique();
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->date('issue_date');
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('draft'); // draft, posted, cancelled
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();
            $table->index('status');
            $table->index('issue_date');
            $table->index('work_order_id');
            $table->index(['status', 'issue_date']);
        });

        // Goods Issue Items
        Schema::create('goods_issue_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_issue_id');
            $table->unsignedBigInteger('sparepart_id');
            $table->unsignedBigInteger('warehouse_location_id')->nullable();
            $table->integer('qty_issued')->default(0);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('total_price', 18, 2)->default(0);

            $table->foreign('goods_issue_id')->references('id')->on('goods_issues')->cascadeOnDelete();
            $table->foreign('sparepart_id')->references('id')->on('spareparts');
            $table->foreign('warehouse_location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->index(['goods_issue_id', 'sparepart_id']);
        });

        // Stock Movements
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->date('movement_date');
            $table->unsignedBigInteger('sparepart_id');
            $table->unsignedBigInteger('warehouse_location_id')->nullable();
            $table->string('movement_type', 20); // in, out
            $table->string('reference_type', 50); // goods_receipt, goods_issue
            $table->unsignedBigInteger('reference_id');
            $table->integer('qty_in')->default(0);
            $table->integer('qty_out')->default(0);
            $table->integer('balance_after')->default(0);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('sparepart_id')->references('id')->on('spareparts');
            $table->foreign('warehouse_location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->index(['sparepart_id', 'movement_date']);
            $table->index('movement_date');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('goods_issue_items');
        Schema::dropIfExists('goods_issues');
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
    }
};
