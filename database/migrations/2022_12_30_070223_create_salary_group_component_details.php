<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryGroupComponentDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.SALARY_GROUP_COMPONENTS_DETAILS_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
        	$table->integer('i_salary_group_id');
        	$table->integer('i_salary_components_id');
        	$table->enum('e_type',[config('constants.SALARY_COMPONENT_TYPE_EARNING'),config('constants.SALARY_COMPONENT_TYPE_DEDUCTION')]);
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
        Schema::dropIfExists(config('constants.SALARY_GROUP_COMPONENTS_DETAILS_TABLE'));
    }
}
