<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Disable FK checks so we can safely drop tables in any order
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Approval Settings - configurable approval levels & budget thresholds
        Schema::dropIfExists('approval_logs');
        Schema::dropIfExists('approval_settings');
        Schema::create('approval_settings', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 30); // pr, po, wo, gi
            $table->string('level_name', 50); // Foreman, Superintendent, Manager
            $table->integer('level_order')->default(1);
            $table->decimal('min_budget', 18, 2)->default(0);
            $table->decimal('max_budget', 18, 2)->nullable(); // null = unlimited
            $table->unsignedBigInteger('approver_user_id')->nullable();
            $table->string('approver_role', 30)->nullable(); // or role-based
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('approver_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['document_type', 'level_order']);
            $table->index(['document_type', 'min_budget']);
        });

        // Approval Logs - track multi-level approvals
        Schema::dropIfExists('approval_logs');
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 30);
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('approval_setting_id')->nullable();
            $table->string('level_name', 50);
            $table->integer('level_order')->default(1);
            $table->string('action', 20); // approved, rejected, pending
            $table->unsignedBigInteger('acted_by')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('approval_setting_id')->references('id')->on('approval_settings')->nullOnDelete();
            $table->foreign('acted_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['document_type', 'document_id']);
            $table->index('acted_by');
        });

        // Notifications
        Schema::dropIfExists('notifications');
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 50); // approval_request, approval_done, stock_alert, license_expiry
            $table->string('title', 150);
            $table->text('message')->nullable();
            $table->string('link', 255)->nullable();
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });

        // Stock Opname
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number', 30)->unique();
            $table->date('opname_date');
            $table->unsignedBigInteger('warehouse_location_id')->nullable();
            $table->string('status', 20)->default('draft'); // draft, in_progress, completed
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('conducted_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->foreign('conducted_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->index('status');
            $table->index('opname_date');
        });

        // Stock Opname Items
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_opname_id');
            $table->unsignedBigInteger('sparepart_id');
            $table->integer('system_qty')->default(0);
            $table->integer('physical_qty')->default(0);
            $table->integer('difference')->default(0);
            $table->text('notes')->nullable();

            $table->foreign('stock_opname_id')->references('id')->on('stock_opnames')->cascadeOnDelete();
            $table->foreign('sparepart_id')->references('id')->on('spareparts');
            $table->index(['stock_opname_id', 'sparepart_id']);
        });

        // Warehouse Transfers
        Schema::dropIfExists('warehouse_transfer_items');
        Schema::dropIfExists('warehouse_transfers');
        Schema::create('warehouse_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number', 30)->unique();
            $table->unsignedBigInteger('from_location_id');
            $table->unsignedBigInteger('to_location_id');
            $table->date('transfer_date');
            $table->string('status', 20)->default('draft'); // draft, posted, cancelled
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->foreign('from_location_id')->references('id')->on('warehouse_locations');
            $table->foreign('to_location_id')->references('id')->on('warehouse_locations');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();
            $table->index('status');
            $table->index('transfer_date');
        });

        // Warehouse Transfer Items
        Schema::create('warehouse_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_transfer_id');
            $table->unsignedBigInteger('sparepart_id');
            $table->integer('qty')->default(0);

            $table->foreign('warehouse_transfer_id')->references('id')->on('warehouse_transfers')->cascadeOnDelete();
            $table->foreign('sparepart_id')->references('id')->on('spareparts');
            $table->index(['warehouse_transfer_id', 'sparepart_id'], 'wti_transfer_sparepart_idx');
        });

        // Add is_consumable flag to spareparts
        if (!Schema::hasColumn('spareparts', 'is_consumable')) {
            Schema::table('spareparts', function (Blueprint $table) {
                $table->boolean('is_consumable')->default(false)->after('uom');
            });
        }

        // Add qty_received tracking to purchase_order_items (for partial GR)
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'qty_received')) {
                $table->integer('qty_received')->default(0)->after('total_price');
            }
            if (!Schema::hasColumn('purchase_order_items', 'qty_outstanding')) {
                $table->integer('qty_outstanding')->default(0)->after('qty_received');
            }
        });

        // Add total_amount to purchase_requests (for budget-based approval)
        if (!Schema::hasColumn('purchase_requests', 'estimated_total')) {
            Schema::table('purchase_requests', function (Blueprint $table) {
                $table->decimal('estimated_total', 18, 2)->default(0)->after('remarks');
            });
        }

        // Re-enable FK checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn('estimated_total');
        });
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['qty_received', 'qty_outstanding']);
        });
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropColumn('is_consumable');
        });
        Schema::dropIfExists('warehouse_transfer_items');
        Schema::dropIfExists('warehouse_transfers');
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('approval_logs');
        Schema::dropIfExists('approval_settings');
    }
};
