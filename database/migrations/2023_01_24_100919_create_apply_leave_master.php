<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplyLeaveMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.APPLY_LEAVE_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_leave_type_id');
            $table->date('dt_leave_from_date');
            $table->date('dt_leave_to_date');
           	$table->enum('e_leave_from_type', [config('constants.FIRST_HALF_LEAVE'),config('constants.SECOND_HALF_LEAVE')])->nullable();
           	$table->enum('e_leave_to_type', [config('constants.FIRST_HALF_LEAVE'),config('constants.SECOND_HALF_LEAVE')])->nullable();
           	$table->enum('e_full_day', [config('constants.FULL_DAY_LEAVE')])->nullable();
           	$table->enum('e_leave_full_type', [config('constants.FIRST_HALF_LEAVE'),config('constants.SECOND_HALF_LEAVE')])->nullable();
           	$table->longText('v_leave_note');
            $table->longText('v_file')->nullable();
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
        Schema::dropIfExists(config('constants.APPLY_LEAVE_MASTER_TABLE'));
    }
}
