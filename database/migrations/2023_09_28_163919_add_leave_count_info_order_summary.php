<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveCountInfoOrderSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.ATTENDANCE_SUMMARY_TABLE'), function (Blueprint $table) {
    		$table->double('d_paid_full_leave_count')->after('d_unpaid_leave_count')->nullable();
    		$table->double('d_paid_half_leave_count')->after('d_unpaid_leave_count')->nullable();
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
