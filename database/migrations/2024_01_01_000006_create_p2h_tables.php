<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Operators
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->string('operator_code', 30)->unique();
            $table->string('operator_name', 100);
            $table->string('nik', 30)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('license_type', 30)->nullable(); // SIM B2, SIO, dll
            $table->date('license_expiry')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('license_expiry');
        });

        // P2H Header
        Schema::create('p2h_checks', function (Blueprint $table) {
            $table->id();
            $table->string('p2h_number', 30)->unique();
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('operator_id');
            $table->date('check_date');
            $table->string('shift', 20)->default('day'); // day, night
            $table->decimal('hour_meter_start', 12, 2)->default(0);
            $table->decimal('km_start', 12, 2)->default(0);
            $table->string('overall_status', 20)->default('layak'); // layak, tidak_layak, layak_catatan
            $table->text('general_notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('operator_id')->references('id')->on('operators');
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->index('check_date');
            $table->index('overall_status');
            $table->index(['unit_id', 'check_date']);
            $table->index(['operator_id', 'check_date']);
        });

        // P2H Check Items
        Schema::create('p2h_check_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('p2h_check_id');
            $table->string('category', 50); // engine, hydraulic, electrical, body, safety, tire, brake, dll
            $table->string('check_item', 150);
            $table->string('condition', 20)->default('good'); // good, warning, bad, na
            $table->text('notes')->nullable();

            $table->foreign('p2h_check_id')->references('id')->on('p2h_checks')->cascadeOnDelete();
            $table->index('p2h_check_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p2h_check_items');
        Schema::dropIfExists('p2h_checks');
        Schema::dropIfExists('operators');
    }
};
