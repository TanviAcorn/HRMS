<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSavedLeaveInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::create(config('constants.ACADEMIC_SAVED_LEAVE_INFO'), function (Blueprint $table) {
    		$table->increments('i_id');
    		$table->longText('i_employee_id');
    		$table->longText('v_year');
    		$table->double('d_save_leave');
    		$table->longText('v_info')->nullable();
    		$table->tinyInteger('t_is_active')->default(1);
    		$table->tinyInteger('t_is_deleted')->default(0);
    		$table->integer('i_created_id');
    		$table->dateTime('dt_created_at');
    		$table->integer('i_updated_id')->nullable();;
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
    	Schema::dropIfExists(ig('constants.ACADEMIC_SAVED_LEAVE_INFO'));
    }
}
