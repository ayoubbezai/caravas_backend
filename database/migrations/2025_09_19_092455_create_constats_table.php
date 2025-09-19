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
        Schema::create('constats', function (Blueprint $table) {
            $table->id();
            $table->string('pdf_url'); // URL or path to the PDF
            $table->string('pdf_hash', 64); // PDF hash (SHA-256)
            $table->decimal('latitude', 10, 7)->nullable(); // GPS latitude
            $table->decimal('longitude', 10, 7)->nullable(); // GPS longitude

            // Array of attachments URLs
            $table->json('attachments_urls')->nullable();

            // Foreign keys for drivers
            $table->unsignedBigInteger('driver_a_id');
            $table->unsignedBigInteger('driver_b_id')->nullable(); // nullable

            // Foreign keys for companies
            $table->unsignedBigInteger('company_a_id');
            $table->unsignedBigInteger('company_b_id')->nullable(); // nullable

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('driver_a_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('driver_b_id')->references('id')->on('drivers')->onDelete('set null');
            $table->foreign('company_a_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('company_b_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constats');
    }
};
