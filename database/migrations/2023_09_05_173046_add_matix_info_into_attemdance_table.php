<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMatixInfoIntoAttemdanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.EMPLOYEE_ATTENDANCE_TABLE'), function (Blueprint $table) {
    		$table->datetime('dt_matrix_start_time')->after('dt_date')->nullable();
    		$table->datetime('dt_matrix_end_time')->after('dt_date')->nullable();
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
