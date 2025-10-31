<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeeklyOffMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.WEEKLY_OFF_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->longText('v_weekly_off_name');
            $table->longText('v_description')->nullable();
            $table->tinyInteger('t_is_monday')->default('0');
            $table->tinyInteger('t_is_tuesday')->default('0');
            $table->tinyInteger('t_is_wednesday')->default('0');
            $table->tinyInteger('t_is_thursday')->default('0');
            $table->tinyInteger('t_is_friday')->default('0');
            $table->tinyInteger('t_is_saturday')->default('0');
            $table->tinyInteger('t_is_sunday')->default('0');
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
        Schema::dropIfExists(config('constants.WEEKLY_OFF_MASTER_TABLE'));
    }
}
