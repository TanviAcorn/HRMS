<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnIntoEmployeeUpdateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config("constants.EMPLOYEE_DESIGNATION_HISTORY"), function (Blueprint $table) {
    		$table->enum('e_record_type' , [ config('constants.DESIGNATION_LOOKUP') , config('constants.TEAM_LOOKUP') , config('constants.SHIFT_RECORD_TYPE') , config('constants.WEEK_OFF_RECORD_TYPE') ])->after('dt_end_date')->nullable();
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
