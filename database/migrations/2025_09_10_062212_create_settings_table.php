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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->text('fields')->nullable();
            $table->text('banner')->nullable();
            $table->text('favicon')->nullable();
            $table->text('smtp')->nullable();
            $table->json('form_builder')->nullable();
            $table->tinyInteger('stripe_test_mode')->nullable()->default(1);
            $table->tinyInteger('stripe_active')->nullable()->default(0);
            $table->text('stripe_test')->nullable();
            $table->text('stripe_live')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
