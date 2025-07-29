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
        Schema::table('running_hours', function (Blueprint $table) {
            $table->decimal('actual_running_hour', 8, 2)->nullable()->after('increase_running_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('running_hours', function (Blueprint $table) {
            $table->dropColumn('actual_running_hour');
        });
    }
};
