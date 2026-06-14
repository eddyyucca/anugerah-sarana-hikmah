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
        Schema::create('operator_warning_letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_no', 30)->unique();
            $table->date('letter_date');
            $table->foreignId('operator_performance_record_id')->constrained('operator_performance_records');
            $table->foreignId('operator_id')->constrained('operators');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('work_order_id')->constrained('work_orders');
            $table->string('year_month', 7);
            $table->decimal('budget_limit', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->decimal('excess_amount', 15, 2);
            $table->text('violation_description');
            $table->string('created_by');
            $table->dateTime('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_warning_letters');
    }
};
