<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSummaryColumnIntoResignEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config("constants.EMPLOYEE_RESIGN_HISTORY"), function (Blueprint $table) {
    		$table->longText('v_discuss_summary')->after('i_termination_reason_id')->nullable();
    		$table->integer('i_employee_id')->after('i_id')->nullable();
    		$table->enum('e_status' , [ config('constants.PENDING_STATUS') , config('constants.APPROVED_STATUS') , config('constants.REJECTED_STATUS') , config('constants.CANCELLED_STATUS') ] )->after('v_discuss_summary')->default(config('constants.PENDING_STATUS') );
    		$table->date('dt_system_last_working_date')->after('dt_last_working_date')->nullable();
    		$table->integer('i_approved_by_id')->after('dt_system_last_working_date')->nullable();
    		$table->dateTime('dt_approved_at')->after('i_approved_by_id')->nullable();
    		$table->longText('v_approval_remark')->after('dt_approved_at')->nullable();
    		
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
