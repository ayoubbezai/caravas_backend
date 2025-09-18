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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();

            $table->text('first_name');
            $table->text('last_name');
            $table->date('date_of_birth');
            $table->text('address');
            $table->text('postal_code');
            $table->text('city');
            $table->text('country');
            $table->text('phone');

            $table->boolean('is_created_by_typing')->default(false);

            // Link to users table
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
