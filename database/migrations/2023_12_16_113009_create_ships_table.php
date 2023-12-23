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
        Schema::create('ships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('departure')->nullable();
            $table->string('arrival')->nullable();
            $table->string('departure_time')->nullable();
            $table->string('arrival_time')->nullable();
            $table->string('ticket_price')->nullable();
            $table->string('description');
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ships');
    }
};
