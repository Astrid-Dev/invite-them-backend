<?php

use App\Enums\GuestConfirmationStatus;
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
        Schema::create('guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid('table_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->string('hint')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->unsignedTinyInteger('seats')
                ->default(1);
            $table->enum('confirmation_status', GuestConfirmationStatus::values())
                ->comment(implode(',', GuestConfirmationStatus::values()))
                ->default(GuestConfirmationStatus::PENDING->value);
            $table->boolean('has_send_email_invitation')
                ->default(false);
            $table->boolean('has_send_whatsapp_invitation')
                ->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
