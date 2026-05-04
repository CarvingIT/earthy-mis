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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('related_unit_id')->nullable();
            $table->double('related_unit_quantity')->nullable();
            $table->timestamps();

            // Add foreign key constraint for self-referential relationship
            $table->foreign('related_unit_id')
                ->references('id')
                ->on('units')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
