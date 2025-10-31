<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryComponentsMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.SALARY_COMPONENTS_MASTER_TABLE'), function (Blueprint $table) {
           	$table->increments('i_id');
        	$table->longText('v_component_name');
        	$table->longText('v_component_description')->nullable();
        	$table->enum('e_salary_components_type', [config('constants.SALARY_COMPONENT_TYPE_EARNING'),config('constants.SALARY_COMPONENT_TYPE_DEDUCTION')]);
        	$table->enum('e_salary_components_frequence', [config('constants.SALARY_COMPONENT_FREQUENCY_MONTHLY'),config('constants.SALARY_COMPONENT_FREQUENCY_YEARLY')])->default(config('constants.SALARY_COMPONENT_FREQUENCY_MONTHLY'));
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
        Schema::dropIfExists(config('constants.SALARY_COMPONENTS_MASTER_TABLE'));
    }
}
