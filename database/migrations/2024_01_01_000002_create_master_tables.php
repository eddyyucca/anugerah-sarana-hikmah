<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Unit Categories
        Schema::create('unit_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // Sparepart Categories
        Schema::create('sparepart_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // Warehouse Locations
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // Units
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code', 30)->unique();
            $table->string('unit_model', 100);
            $table->string('unit_type', 50)->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('department', 80)->nullable();
            $table->string('current_status', 30)->default('available'); // available, under_repair, standby
            $table->decimal('hour_meter', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('unit_categories')->nullOnDelete();
            $table->index('current_status');
            $table->index('department');
            $table->index('is_active');
            $table->index(['current_status', 'is_active']);
        });

        // Spareparts
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            $table->string('part_number', 50)->unique();
            $table->string('part_name', 150);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('stock_on_hand')->default(0);
            $table->string('uom', 20)->default('PCS');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('sparepart_categories')->nullOnDelete();
            $table->index('category_id');
            $table->index('is_active');
            $table->index('stock_on_hand');
        });

        // Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code', 30)->unique();
            $table->string('supplier_name', 150);
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        // Technicians
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->string('technician_code', 30)->unique();
            $table->string('technician_name', 100);
            $table->string('skill', 80)->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technicians');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('spareparts');
        Schema::dropIfExists('units');
        Schema::dropIfExists('warehouse_locations');
        Schema::dropIfExists('sparepart_categories');
        Schema::dropIfExists('unit_categories');
    }
};
