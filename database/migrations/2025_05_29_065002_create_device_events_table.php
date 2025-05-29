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
        Schema::create('device_events', function (Blueprint $table) {
            $table->id();
            $table->string('deviceName');
            $table->string('deviceId');
            $table->string('moduleId');
            $table->string('eventField');
            $table->string('siteId');
            $table->float('lowerLimit')->nullable();
            $table->float('upperLimit')->nullable();
            $table->string('lowerLimitMsg')->nullable();
            $table->string('upperLimitMsg')->nullable();
            $table->string('userEmail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_events');
    }
};
