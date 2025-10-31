<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaritalStatusToEmployeeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
            $table->enum('e_marital_status',[config('constants.MARRIED_STATUS'),config('constants.UNMARRIED_STATUS')])->after('e_employment_status')->nullable();
            $table->longText('v_emergency_contact_person_name')->after('e_marital_status')->nullable();
            $table->longText('v_emergency_contact_relation')->after('v_emergency_contact_person_name')->nullable();
            $table->longText('v_emergency_contact_person_no')->after('v_emergency_contact_relation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
