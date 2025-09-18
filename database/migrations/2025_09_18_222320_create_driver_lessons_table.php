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
        Schema::create('driver_lessons', function (Blueprint $table) {
            $table->id();

            // Foreign key to drivers table
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');

            // Fields from your image
            $table->string('last_name');
            $table->string('first_name');
            $table->date('date_of_birth');
            $table->text('address');
            $table->text('postal_code');
            $table->text('city');
            $table->string('country');
            $table->string('license_number');
            $table->string('license_category'); // A, B, C, etc.
            $table->date('license_valid_until');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_lessons');
    }
};
