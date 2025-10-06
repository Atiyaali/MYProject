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
        Schema::create('participant_activities', function (Blueprint $table) {
            $table->id();
            $table->string('event')->default(null)->nullable();
            $table->string('time')->default(null)->nullable();
            $table->string('type')->default('visit')->nullable();
            $table->integer('participant_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_activities');
    }
};
