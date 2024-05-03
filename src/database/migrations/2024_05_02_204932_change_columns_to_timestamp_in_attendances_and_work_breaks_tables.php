<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsToTimestampInAttendancesAndWorkBreaksTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances,work_breaks', function (Blueprint $table) {
            $table->timestamp('start_work')->change();
            $table->timestamp('end_work')->change();
            $table->timestamp('start_break')->change();
            $table->timestamp('end_break')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances,work_breaks', function (Blueprint $table) {
            $table->dateTime('start_work')->change();
            $table->dateTime('end_work')->change();
            $table->dateTime('start_break')->change();
            $table->dateTime('end_break')->change();
        });
    }
}
