<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Role Menu Permissions
        Schema::create('menu_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role', 30);
            $table->string('menu_key', 50); // dashboard, units, spareparts, pr, po, gr, gi, wo, etc
            $table->boolean('can_view')->default(false);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_approve')->default(false);
            $table->timestamps();

            $table->unique(['role', 'menu_key']);
            $table->index('role');
        });

        // Warehouse Stocks - stock per sparepart per location
        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sparepart_id');
            $table->unsignedBigInteger('warehouse_location_id');
            $table->integer('qty')->default(0);
            $table->timestamps();

            $table->foreign('sparepart_id')->references('id')->on('spareparts');
            $table->foreign('warehouse_location_id')->references('id')->on('warehouse_locations');
            $table->unique(['sparepart_id', 'warehouse_location_id']);
            $table->index('warehouse_location_id');
        });

        // Add received_by to warehouse_transfers
        Schema::table('warehouse_transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('received_by')->nullable()->after('posted_at');
            $table->timestamp('received_at')->nullable()->after('received_by');
            $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
        });

        // Add department/warehouse_location_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('department', 50)->nullable()->after('role'); // logistic, plant, warehouse, purchasing, admin
            $table->unsignedBigInteger('warehouse_location_id')->nullable()->after('department');
            $table->foreign('warehouse_location_id')->references('id')->on('warehouse_locations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['warehouse_location_id']);
            $table->dropColumn(['department', 'warehouse_location_id']);
        });
        Schema::table('warehouse_transfers', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn(['received_by', 'received_at']);
        });
        Schema::dropIfExists('warehouse_stocks');
        Schema::dropIfExists('menu_permissions');
    }
};
