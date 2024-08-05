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
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('location');
            $table->dropColumn('country');

            $table->unsignedBigInteger('address_id');

            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('address');
            $table->string('location');
            $table->string('country');

            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
        });
    }
};
