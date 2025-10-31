<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHalfLeaveColumnIntoApplyLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.APPLY_LEAVE_MASTER_TABLE'), function (Blueprint $table) {
    		$table->tinyInteger('t_is_half_leave')->after('v_file')->default('0');
    		$table->dropColumn('e_leave_from_type');
    		$table->dropColumn('e_leave_to_type');
    		$table->dropColumn('e_full_day');
    		$table->dropColumn('e_leave_full_type');
    		$table->enum('e_from_duration',[config('constants.FIRST_HALF_LEAVE'),config('constants.SECOND_HALF_LEAVE')])->after('dt_leave_to_date')->nullable();
    		$table->enum('e_to_duration',[config('constants.FIRST_HALF_LEAVE'),config('constants.SECOND_HALF_LEAVE')])->after('dt_leave_to_date')->nullable();
    		$table->enum('e_duration',[config('constants.FIRST_HALF_LEAVE'),config('constants.SECOND_HALF_LEAVE'),config('constants.FULL_DAY_LEAVE')])->after('dt_leave_to_date')->nullable();
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
