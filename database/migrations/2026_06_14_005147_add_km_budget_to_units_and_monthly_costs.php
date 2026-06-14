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
        Schema::table('units', function (Blueprint $table) {
            $table->decimal('monthly_km_budget', 10, 2)->nullable()->after('monthly_budget_limit');
        });

        Schema::table('unit_monthly_costs', function (Blueprint $table) {
            $table->decimal('total_km', 12, 2)->default(0)->after('work_order_count');
            $table->boolean('is_over_km_budget')->default(false)->after('total_km');
            $table->dateTime('km_exceeded_at')->nullable()->after('is_over_km_budget');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('monthly_km_budget');
        });

        Schema::table('unit_monthly_costs', function (Blueprint $table) {
            $table->dropColumn(['total_km', 'is_over_km_budget', 'km_exceeded_at']);
        });
    }
};
