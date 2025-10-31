<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeeklyOffInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create(config('constants.WEEKLY_OFF_INFO_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_weekly_off_master_id');
            $table->enum('v_monday_all_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_monday_alternate_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_tuesday_all_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_tuesday_alternate_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_wednesday_all_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_wednesday_alternate_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_thursday_all_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_thursday_alternate_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_friday_all_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_friday_alternate_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_saturday_all_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_saturday_alternate_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_sunday_all_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
            $table->enum('v_sunday_alternate_off' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ] )->default( config('constants.SELECTION_NO') );
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
        Schema::dropIfExists(config('constants.WEEKLY_OFF_INFO_TABLE'));
    }
}
