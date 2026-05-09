<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operator_performance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operator_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('work_order_id');
            $table->string('year_month', 7); // Format: 'YYYY-MM'
            $table->decimal('monthly_budget_limit', 15, 2);
            $table->decimal('total_cost_at_exceedance', 15, 2);
            $table->decimal('excess_amount', 15, 2);
            $table->timestamp('recorded_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('operator_id')->references('id')->on('operators')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();
            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->index(['operator_id', 'year_month']);
            $table->index(['unit_id', 'year_month']);
            $table->index('year_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_performance_records');
    }
};
