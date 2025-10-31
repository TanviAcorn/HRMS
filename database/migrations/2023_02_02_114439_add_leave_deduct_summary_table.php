<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveDeductSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::create(config('constants.LEAVE_SUMMARY_TABLE'), function (Blueprint $table) {
    		$table->increments('i_id');
    		$table->integer('i_employee_id');
    		$table->integer('i_leave_type_id');
    		$table->enum('e_leave_type', [config('constants.EARN_LEAVE'),config('constants.DEDUCT_LEAVE')])->nullable();
    		$table->enum('e_leave_mode', [config('constants.PAID_LEAVE'),config('constants.UNPAID_LEAVE')])->nullable();
    		$table->date('dt_added_used_at');
    		$table->date('dt_applicable_at')->nullable();
    		$table->integer('i_apply_leave_id')->nullable();
    		$table->integer('d_current_before_balance')->nullable();
    		$table->integer('d_current_balance')->nullable();
    		$table->integer('d_current_after_balance')->nullable();
    		$table->longText('v_year')->nullable();
    		$table->tinyInteger('t_is_active')->default('1');
    		$table->tinyInteger('t_is_deleted')->default('0');
    		$table->integer('i_created_id');
    		$table->dateTime('dt_created_at');
    		$table->integer('i_updated_id')->nullable();
    		$table->dateTime('dt_updated_at')->nullable();
    		$table->integer('i_deleted_id')->nullable();
    		$table->dateTime('dt_deleted_at')->nullable();
    		$table->ipAddress('v_ip')->nullable();
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
