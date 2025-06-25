<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('running_hours', function (Blueprint $table) {
        $table->unsignedBigInteger('site_id')->after('id')->nullable(); 
    });
}

public function down()
{
    Schema::table('running_hours', function (Blueprint $table) {
        $table->dropColumn('site_id');
    });
}

};
