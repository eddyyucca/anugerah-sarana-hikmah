<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->enum('repair_location', ['di_workshop', 'di_luar_workshop'])->default('di_workshop')->after('unit_id');
            $table->text('complaint')->nullable()->change();
            $table->unsignedBigInteger('complaint_type_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('repair_location');
        });
    }
};
