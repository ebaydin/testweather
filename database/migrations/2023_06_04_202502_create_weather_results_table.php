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
        Schema::create('weather_results', function (Blueprint $table) {
            $table->id('result_id');
            $table->unsignedBigInteger('query_id');
            $table->unsignedBigInteger('date');
            $table->string('description');
            $table->float('max_c');
            $table->float('min_c');
            $table->integer('pressure');
            $table->float('maxwind_ms');
            $table->integer('humidity');
            $table->float('uv');
            $table->string('icon');
            $table->timestamps();

            $table->foreign('query_id')->references('query_id')->on('weather_queries')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_results');
    }
};
