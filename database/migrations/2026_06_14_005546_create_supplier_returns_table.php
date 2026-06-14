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
        Schema::create('supplier_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no', 30)->unique();
            $table->date('return_date');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('goods_receipt_id')->nullable()->constrained('goods_receipts')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->text('return_reason');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'sent'])->default('draft');
            $table->string('confirmed_by')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('supplier_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sparepart_id')->constrained('spareparts');
            $table->decimal('qty_returned', 10, 2);
            $table->decimal('qty_received_original', 10, 2)->default(0);
            $table->string('defect_reason', 200);
            $table->text('condition_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_return_items');
        Schema::dropIfExists('supplier_returns');
    }
};
