<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeRelationDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.EMPLOYEE_RELATION_DETAILS_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_employee_id');
            $table->enum('e_employee_relation', [config('constants.EMPLOYEE_RELATION_FATHER'),config('constants.EMPLOYEE_RELATION_MOTHER'),config('constants.EMPLOYEE_RELATION_SPOUSE')]);
          	$table->longText('v_relation_name');
          	$table->longText('v_mobile_number')->nullable();
          	$table->longText('v_email')->nullable();
          	$table->longText('v_profession')->nullable();
          	$table->date('dt_birth_date')->nullable();
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
        Schema::dropIfExists(config('constants.EMPLOYEE_RELATION_DETAILS_TABLE'));
    }
}
