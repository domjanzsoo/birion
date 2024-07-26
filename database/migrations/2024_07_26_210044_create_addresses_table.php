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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('street');
            $table->string('municipality_sub_division')->nullable();
            $table->string('municipality_secondary_sub_division')->nullable();
            $table->string('municipality');
            $table->string('country');
            $table->string('post_code')->nullable();
            $table->double('lat');
            $table->double('lon');
            $table->integer('house_number')->nullable();
            $table->string('house_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
