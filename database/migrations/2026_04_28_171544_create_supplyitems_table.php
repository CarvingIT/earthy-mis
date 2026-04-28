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
        Schema::create('supplyitems', function (Blueprint $table) {
            $table->id();
            $table->date('Date')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->foreignId('consumable_id')->constrained()->cascadeOnDelete();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplyitems');
    }
};
