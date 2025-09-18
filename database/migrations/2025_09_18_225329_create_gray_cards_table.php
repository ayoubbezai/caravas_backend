<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gray_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->string('card_number')->nullable();
            $table->string('car_name');
            $table->string('car_type');
            $table->timestamps();

            // Ensure one gray card per driver
            $table->unique('driver_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gray_cards');
    }
};
