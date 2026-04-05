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
        Schema::create('complaint_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->string('color', 20)->default('#6c757d'); // for dashboard visualization
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('is_active');
        });

        // Add complaint_type_id to work_orders
        Schema::table('work_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('complaint_type_id')->nullable()->after('complaint');
            $table->foreign('complaint_type_id')->references('id')->on('complaint_types')->nullOnDelete();
            $table->index('complaint_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['complaint_type_id']);
            $table->dropColumn('complaint_type_id');
        });
        Schema::dropIfExists('complaint_types');
    }
};
