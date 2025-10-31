<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnIntoApplyLeaveTable extends Migration
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
    		$table->tinyInteger('t_is_multiple')->after('v_file')->default('0');
    		$table->enum('e_status',[config('constants.PENDING_STATUS'),config('constants.APPROVED_STATUS'),config('constants.REJECTED_STATUS'),config('constants.CANCELLED_STATUS')])->after('v_file')->default( config('constants.PENDING_STATUS') );
    		$table->tinyInteger('d_no_of_paid_leave')->after('v_file')->default(0);
    		$table->tinyInteger('d_no_of_unpaid_leave')->after('v_file')->default(0);
    		$table->dateTime('dt_approved_at')->after('v_file')->nullable();
    		$table->integer('i_approved_by_id')->after('v_file')->nullable();
    		$table->longText('v_approve_reject_remark')->after('v_file')->nullable();
    		$table->longText('v_year')->after('v_file')->nullable();
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
