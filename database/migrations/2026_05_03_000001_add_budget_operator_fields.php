<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->decimal('monthly_budget_limit', 15, 2)->nullable()->after('hour_meter')
                ->comment('Batas biaya perbaikan per bulan. NULL = tidak ada batas.');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('operator_id')->nullable()->after('unit_id');
            $table->foreign('operator_id')->references('id')->on('operators')->nullOnDelete();
            $table->index('operator_id');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('monthly_budget_limit');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['operator_id']);
            $table->dropIndex(['operator_id']);
            $table->dropColumn('operator_id');
        });
    }
};
