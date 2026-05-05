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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'base_unit_id')) {
                $table->foreignId('base_unit_id')->nullable()->constrained('units')->nullOnDelete();
            }
            if (!Schema::hasColumn('products', 'sales_unit_id')) {
                $table->foreignId('sales_unit_id')->nullable()->constrained('units')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'base_unit_id')) {
                $table->dropForeignIdFor(\App\Models\Unit::class, 'base_unit_id');
            }
            if (Schema::hasColumn('products', 'sales_unit_id')) {
                $table->dropForeignIdFor(\App\Models\Unit::class, 'sales_unit_id');
            }
        });
    }
};
