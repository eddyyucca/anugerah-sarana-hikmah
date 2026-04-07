<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fit to Work Checks (Pemeriksaan Kesehatan Operator)
        Schema::create('fit_to_work_checks', function (Blueprint $table) {
            $table->id();
            $table->string('ftw_number', 30)->unique();
            $table->unsignedBigInteger('operator_id');
            $table->date('check_date');
            $table->enum('shift', ['day', 'night'])->default('day');
            $table->string('blood_pressure', 20)->nullable(); // ex: 120/80
            $table->enum('general_condition', ['fit', 'unfit'])->default('fit');
            $table->boolean('alcohol_test')->default(false); // false = negatif, true = terdeteksi
            $table->boolean('is_fit')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamps();

            $table->foreign('operator_id')->references('id')->on('operators');
            $table->foreign('checked_by')->references('id')->on('users')->nullOnDelete();
            $table->index('check_date');
            $table->index(['operator_id', 'check_date']);
        });

        // Timesheets (Laporan Akhir Shift)
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->string('ts_number', 30)->unique();
            $table->unsignedBigInteger('p2h_check_id')->unique(); // 1 P2H hanya 1 Timesheet
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('operator_id');
            $table->date('shift_date');
            $table->enum('shift', ['day', 'night'])->default('day');
            $table->decimal('hour_meter_start', 12, 2)->default(0); // disalin dari P2H
            $table->decimal('hour_meter_end', 12, 2)->default(0);
            $table->decimal('working_hours', 8, 2)->default(0); // hm_end - hm_start
            $table->unsignedInteger('retase')->default(0); // jumlah trip
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamps();

            $table->foreign('p2h_check_id')->references('id')->on('p2h_checks');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('operator_id')->references('id')->on('operators');
            $table->foreign('submitted_by')->references('id')->on('users')->nullOnDelete();
            $table->index('shift_date');
            $table->index(['unit_id', 'shift_date']);
            $table->index(['operator_id', 'shift_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('fit_to_work_checks');
    }
};
