<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
 		Schema::create(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->enum('e_auto_generate_no',[config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->default(config('constants.SELECTION_YES'));
            $table->longText('v_employee_code');
            $table->longText('v_employee_name');
            $table->longText('v_employee_full_name');
            $table->enum('e_gender',[config('constants.GENDER_FEMALE'),config('constants.GENDER_MALE')]);
            $table->longText('v_blood_group')->nullable();
            $table->date('dt_birth_date');
            $table->longText('v_outlook_email_id')->nullable();
            $table->longText('v_personal_email_id');
            $table->longText('v_contact_no');
            $table->longText('v_current_address_line_first');
            $table->longText('v_current_address_line_second')->nullable();
            $table->integer('i_current_address_city_id');
            $table->longText('v_current_address_pincode')->nullable();
            $table->longText('v_permanent_address_line_first');
            $table->longText('v_permanent_address_line_second')->nullable();
            $table->integer('i_permanent_address_city_id');
            $table->longText('v_permanent_address_pincode')->nullable();
            $table->longText('v_aadhar_no');
            $table->longText('v_pan_no')->nullable();
            $table->date('dt_joining_date');
            $table->integer('i_designation_id');
            $table->integer('i_team_id');
            $table->integer('i_leader_id')->nullable();
            $table->integer('i_recruitment_source_id');
            $table->integer('i_reference_emp_id')->nullable();
            $table->integer('i_shift_id')->nullable();
            $table->integer('i_weekoff_id')->nullable();
            $table->integer('i_probation_period_id')->nullable();
            $table->integer('i_notice_period_id');
            $table->integer('i_bank_id')->nullable();
            $table->longText('v_bank_account_no')->nullable();
            $table->longText('v_bank_account_ifsc_code')->nullable();
            $table->longText('v_uan_no')->nullable();
            $table->enum('e_assign_salary',[config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->nullable();
            $table->enum('e_employment_status',[config('constants.PROBATION_EMPLOYMENT_STATUS'),config('constants.CONFIRMED_EMPLOYMENT_STATUS'),config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS'),config('constants.RELIEVED_EMPLOYMENT_STATUS'),config('constants.SUSPENDED_EMPLOYMENT_STATUS')])->nullable();
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
    	Schema::dropIfExists(config('constants.EMPLOYEE_MASTER_TABLE'));
    }
}
