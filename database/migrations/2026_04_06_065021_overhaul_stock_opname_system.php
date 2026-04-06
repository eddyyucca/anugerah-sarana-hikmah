<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add bin_location to spareparts
        Schema::table('spareparts', function (Blueprint $table) {
            $table->string('bin_location', 30)->nullable()->after('part_name');
        });

        // 2. Overhaul stock_opnames table
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['warehouse_location_id']);
            $table->dropColumn('warehouse_location_id');
            $table->unsignedBigInteger('submitted_by')->nullable()->after('conducted_by');
            $table->timestamp('submitted_at')->nullable()->after('submitted_by');
            $table->foreign('submitted_by')->references('id')->on('users')->nullOnDelete();
        });

        // 3. Add counting fields to stock_opname_items
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->boolean('is_counted')->default(false)->after('notes');
            $table->unsignedBigInteger('counted_by')->nullable()->after('is_counted');
            $table->timestamp('counted_at')->nullable()->after('counted_by');
            $table->foreign('counted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->dropForeign(['counted_by']);
            $table->dropColumn(['is_counted', 'counted_by', 'counted_at']);
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropColumn(['submitted_by', 'submitted_at']);
            $table->unsignedBigInteger('warehouse_location_id')->nullable();
            $table->foreign('warehouse_location_id')->references('id')->on('warehouse_locations');
        });

        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropColumn('bin_location');
        });
    }
};
