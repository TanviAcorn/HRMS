<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginalBreakTimeInfoIntoAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config("constants.APPLY_LEAVE_MASTER_TABLE"), function (Blueprint $table) {
    		$table->longText('v_leave_summary')->after('d_no_of_paid_leave')->nullable();
    		$table->longText('v_month_wise_count')->after('v_leave_summary')->nullable();
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
