<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_monthly_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->string('year_month', 7); // Format: 'YYYY-MM'
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->unsignedInteger('work_order_count')->default(0);
            $table->boolean('is_over_budget')->default(false);
            $table->timestamp('exceeded_at')->nullable();
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();
            $table->unique(['unit_id', 'year_month']);
            $table->index(['unit_id', 'year_month']);
            $table->index('is_over_budget');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_monthly_costs');
    }
};
