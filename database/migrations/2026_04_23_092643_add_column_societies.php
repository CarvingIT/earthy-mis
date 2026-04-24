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
        Schema::table('societies', function (Blueprint $table) {
            //
            $table->renameColumn('contact_person','contact_person_email');
            $table->string('joining_month')->nullable();
            $table->string('flats_families')->nullable();
            $table->string('chairman_name')->nullable();
            $table->string('secretary_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            //
            $table->renameColumn('contact_person_email','contact_person');
            $table->dropColumn('joining_month','flats_families','chairman_name','secretary_name');
        });
    }
};
