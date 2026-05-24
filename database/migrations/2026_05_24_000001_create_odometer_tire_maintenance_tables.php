<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom odometer & jumlah roda ke units
        Schema::table('units', function (Blueprint $table) {
            $table->decimal('current_odometer', 12, 2)->default(0)->after('hour_meter');
            $table->tinyInteger('wheel_count')->default(8)->after('current_odometer');
        });

        // Log pembacaan odometer per unit (diisi dari P2H harian)
        Schema::create('unit_odometer_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->decimal('odometer_km', 12, 2);
            $table->decimal('delta_km', 10, 2)->default(0);
            $table->date('reading_date');
            $table->string('source')->default('manual'); // 'p2h' atau 'manual'
            $table->string('recorded_by')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // Ban yang terpasang di unit (diambil dari inventory/sparepart)
        Schema::create('unit_tires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sparepart_id')->constrained('spareparts'); // ban dari inventory
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->tinyInteger('position_number')->nullable();
            $table->string('position_label')->nullable();
            $table->decimal('total_km', 12, 2)->default(0); // km kumulatif ban ini
            $table->decimal('km_limit', 10, 2)->default(40000);
            $table->decimal('odo_when_installed', 12, 2)->nullable(); // odo unit saat pasang
            $table->date('installed_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // Riwayat pemasangan/pelepasan ban
        Schema::create('unit_tire_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_tire_id')->constrained('unit_tires')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units');
            $table->tinyInteger('position_number');
            $table->string('position_label')->nullable();
            $table->decimal('odo_at_install', 12, 2)->default(0);
            $table->decimal('odo_at_remove', 12, 2)->nullable();
            $table->decimal('km_used', 10, 2)->default(0);
            $table->date('installed_at');
            $table->date('removed_at')->nullable();
            $table->string('removed_reason')->nullable();
            $table->timestamps();
        });

        // Item maintenance berbasis km (oli, filter, dll)
        Schema::create('maintenance_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('interval_km', 10, 2);
            $table->decimal('alert_before_km', 10, 2)->default(500);
            $table->foreignId('sparepart_id')->nullable()->constrained('spareparts')->nullOnDelete(); // sparepart yang dipakai (opsional)
            $table->unsignedInteger('qty_per_service')->default(1); // jumlah yang dipakai per service
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Log maintenance yang sudah dilakukan per unit
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_item_id')->constrained('maintenance_items')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('sparepart_id')->nullable()->constrained('spareparts')->nullOnDelete(); // sparepart yg dipakai saat service ini
            $table->unsignedInteger('qty_used')->default(0);
            $table->decimal('odometer_at_service', 12, 2);
            $table->decimal('next_service_km', 12, 2);
            $table->date('service_date');
            $table->string('performed_by')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('maintenance_items');
        Schema::dropIfExists('unit_tire_history');
        Schema::dropIfExists('unit_tires');
        Schema::dropIfExists('unit_odometer_readings');
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['current_odometer', 'wheel_count']);
        });
    }
};
