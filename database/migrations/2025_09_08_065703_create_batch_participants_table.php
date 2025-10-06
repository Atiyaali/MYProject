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
        Schema::create('batch_participant', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('batch_id')->nullable();
            $table->bigInteger('participant_id')->nullable();
            $table->bigInteger('compain_id')->nullable();
            $table->string('status')->default('pending')->nullable();
            $table->text('sent_at')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_participant');
    }
};
