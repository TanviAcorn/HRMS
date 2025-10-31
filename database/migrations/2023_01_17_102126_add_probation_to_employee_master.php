<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProbationToEmployeeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
        	$table->enum('e_in_probation',[config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->default(config('constants.SELECTION_NO'))->after('v_emergency_contact_person_no');
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
