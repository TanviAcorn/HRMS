<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackTimeInfoIntoTimeOffMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.TIME_OFF_MASTER_TABLE'), function (Blueprint $table) {
    		$table->date('dt_time_off_back_date')->after('e_status')->nullable();
    		$table->time('t_from_back_time')->after('dt_time_off_back_date')->nullable();
    		$table->time('t_to_back_time')->after('t_from_back_time')->nullable();
    		
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
