<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveAssignHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.LEAVE_ASSIGN_HISTORY_TABLE'), function (Blueprint $table) {
        	$table->increments('i_id');
        	$table->integer('i_employee_id');
        	$table->integer('i_leave_type_id');
        	$table->date('dt_effective_date');
        	$table->double('d_no_of_days_assign');
        	$table->double('d_no_of_days_used')->default(0);
        	$table->longText('v_remark')->nullable();
        	$table->tinyInteger('t_is_used_status')->default('0');
        	$table->tinyInteger('t_is_add_into_balance')->default('0');
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
        Schema::dropIfExists('leave_assign_history');
    }
}
