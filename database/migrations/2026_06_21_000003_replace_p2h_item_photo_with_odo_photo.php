<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('p2h_check_items', function (Blueprint $table) {
            $table->dropColumn('photo');
        });

        Schema::table('p2h_checks', function (Blueprint $table) {
            $table->string('odo_photo')->nullable()->after('km_start');
        });
    }

    public function down(): void
    {
        Schema::table('p2h_checks', function (Blueprint $table) {
            $table->dropColumn('odo_photo');
        });

        Schema::table('p2h_check_items', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('notes');
        });
    }
};
