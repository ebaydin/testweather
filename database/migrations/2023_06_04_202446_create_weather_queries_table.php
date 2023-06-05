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
        Schema::create('weather_queries', function (Blueprint $table) {
            $table->id('query_id');
            $table->unsignedBigInteger('service_id');
            $table->string('ip_address');
            $table->float('latitude')->default(0);
            $table->float('longitude')->default(0);
            $table->date('date');
            $table->timestamps();

            $table->foreign('service_id')->references('service_id')->on('weather_services')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_queries');
    }
};
