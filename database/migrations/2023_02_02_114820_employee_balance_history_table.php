<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmployeeBalanceHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::create(config('constants.LEAVE_BALANCE_TABLE'), function (Blueprint $table) {
    		$table->increments('i_id');
    		$table->integer('i_employee_id');
    		$table->integer('i_leave_type_id');
    		$table->integer('d_current_balance');
    		$table->integer('d_total_add_balance');
    		$table->integer('d_total_deduct_balance');
    		$table->integer('d_paid_leave_count');
    		$table->integer('d_unpaid_leave_count');
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
