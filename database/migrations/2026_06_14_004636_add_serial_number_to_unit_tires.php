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
        Schema::table('unit_tires', function (Blueprint $table) {
            $table->string('serial_number', 100)->nullable()->unique()->after('sparepart_id');
        });
    }

    public function down(): void
    {
        Schema::table('unit_tires', function (Blueprint $table) {
            $table->dropColumn('serial_number');
        });
    }
};
