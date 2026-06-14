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
        Schema::table('timesheets', function (Blueprint $table) {
            $table->decimal('km_end', 12, 2)->nullable()->after('hour_meter_end');
            $table->decimal('km_traveled', 10, 2)->nullable()->after('km_end');
        });
    }

    public function down(): void
    {
        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropColumn(['km_end', 'km_traveled']);
        });
    }
};
