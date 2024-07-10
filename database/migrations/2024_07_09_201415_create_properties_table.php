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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->smallInteger('room_number');
            $table->enum('heating', ['gas', 'wood', 'electric']);
            $table->string('address');
            $table->string('location');
            $table->string('country');
            $table->smallInteger('size');
            $table->enum('offer_type', ['sale', 'rent']);
            $table->integer('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
