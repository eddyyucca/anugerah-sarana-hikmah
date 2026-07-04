<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timesheets', function (Blueprint $table) {
            // Foto odometer awal & akhir shift
            $table->string('odo_start_photo')->nullable()->after('km_traveled');
            $table->string('odo_end_photo')->nullable()->after('odo_start_photo');

            // Kalkulasi km per ritase & flag anomali
            $table->decimal('km_per_ritase', 8, 2)->nullable()->after('odo_end_photo');
            $table->tinyInteger('odo_discrepancy_flag')->default(0)->after('km_per_ritase');
        });
    }

    public function down(): void
    {
        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropColumn([
                'odo_start_photo',
                'odo_end_photo',
                'km_per_ritase',
                'odo_discrepancy_flag',
            ]);
        });
    }
};
