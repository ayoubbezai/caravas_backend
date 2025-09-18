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
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();

            $table->text('policy_number');       // insurance policy number
            $table->text('company_name');        // name of the insurance company

            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();



            // Link to driver
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');


            $table->boolean('is_created_by_typing')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
