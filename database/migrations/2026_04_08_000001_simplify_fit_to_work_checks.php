<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fit_to_work_checks', function (Blueprint $table) {
            // Hapus kolom detail medis yang tidak diperlukan
            $table->dropColumn(['blood_pressure', 'general_condition', 'alcohol_test']);
            // Tambah 2 pertanyaan sederhana
            $table->boolean('siap_bekerja')->default(true)->after('shift');
            $table->boolean('kondisi_sehat')->default(true)->after('siap_bekerja');
        });
    }

    public function down(): void
    {
        Schema::table('fit_to_work_checks', function (Blueprint $table) {
            $table->dropColumn(['siap_bekerja', 'kondisi_sehat']);
            $table->string('blood_pressure', 20)->nullable();
            $table->enum('general_condition', ['fit', 'unfit'])->default('fit');
            $table->boolean('alcohol_test')->default(false);
        });
    }
};
