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
        Schema::create('tire_damage_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_no', 30)->unique();
            $table->foreignId('unit_tire_id')->constrained('unit_tires');
            $table->foreignId('unit_id')->constrained('units');
            $table->date('report_date');
            $table->decimal('km_at_damage', 12, 2)->nullable();
            $table->decimal('km_used_when_damaged', 10, 2)->nullable();
            $table->date('installed_at')->nullable();
            $table->enum('damage_type', ['puncture', 'sidewall', 'bead', 'tread', 'manufacturing_defect', 'other'])->default('other');
            $table->text('damage_description');
            $table->boolean('is_warranty_claim')->default(false);
            $table->enum('status', ['draft', 'approved'])->default('draft');
            $table->string('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tire_damage_reports');
    }
};
