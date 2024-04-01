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
        Schema::create('i_love_pdf_api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('public_key')->unique();
            $table->string('secret_key')->unique();
            $table->unsignedInteger('remaining_files')->default(250);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('i_love_pdf_api_keys');
    }
};
