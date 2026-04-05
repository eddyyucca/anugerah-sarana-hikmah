<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Work Orders
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number', 30)->unique();
            $table->unsignedBigInteger('unit_id');
            $table->text('complaint')->nullable();
            $table->string('maintenance_type', 30)->default('corrective'); // corrective, preventive, predictive
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->string('status', 20)->default('open'); // open, in_progress, waiting_part, completed, cancelled
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->decimal('downtime_hours', 10, 2)->default(0);
            $table->decimal('labor_cost', 18, 2)->default(0);
            $table->decimal('vendor_cost', 18, 2)->default(0);
            $table->decimal('consumable_cost', 18, 2)->default(0);
            $table->text('action_taken')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('technician_id')->references('id')->on('technicians')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index('status');
            $table->index(['unit_id', 'status']);
            $table->index('start_time');
            $table->index(['status', 'start_time']);
        });

        // Add FK on goods_issues after work_orders created
        Schema::table('goods_issues', function (Blueprint $table) {
            $table->foreign('work_order_id')->references('id')->on('work_orders')->nullOnDelete();
        });

        // Work Order Logs
        Schema::create('work_order_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->datetime('activity_time');
            $table->string('activity_type', 50);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index('work_order_id');
        });

        // Unit Availability Logs - summary table for fast dashboard
        Schema::create('unit_availability_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->date('date');
            $table->decimal('scheduled_hours', 8, 2)->default(24);
            $table->decimal('downtime_hours', 8, 2)->default(0);
            $table->decimal('available_hours', 8, 2)->default(24);
            $table->decimal('availability_percent', 5, 2)->default(100);
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreign('unit_id')->references('id')->on('units');
            $table->index(['unit_id', 'date']);
            $table->index('date');
            $table->unique(['unit_id', 'date', 'reference_type', 'reference_id'], 'uq_avail_log');
        });

        // Repair Cost Summaries - pre-calculated for fast dashboard
        Schema::create('repair_cost_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->unsignedBigInteger('unit_id');
            $table->decimal('sparepart_cost', 18, 2)->default(0);
            $table->decimal('labor_cost', 18, 2)->default(0);
            $table->decimal('vendor_cost', 18, 2)->default(0);
            $table->decimal('consumable_cost', 18, 2)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->timestamps();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->index('unit_id');
            $table->index('work_order_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_cost_summaries');
        Schema::dropIfExists('unit_availability_logs');
        Schema::dropIfExists('work_order_logs');
        Schema::table('goods_issues', function (Blueprint $table) {
            $table->dropForeign(['work_order_id']);
        });
        Schema::dropIfExists('work_orders');
    }
};
