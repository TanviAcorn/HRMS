<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeRelationColumnToEmployeeRelationDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table(config("constants.EMPLOYEE_RELATION_DETAILS_TABLE"), function (Blueprint $table) {
       	$table->enum('e_employee_relation', [config("constants.EMPLOYEE_RELATION_FATHER"),config("constants.EMPLOYEE_RELATION_MOTHER"),config("constants.EMPLOYEE_RELATION_SPOUSE"),config("constants.EMPLOYEE_RELATION_GRAND_MOTHER"),config("constants.EMPLOYEE_RELATION_GRAND_FATHER"),config("constants.EMPLOYEE_RELATION_BROTHER"),config("constants.EMPLOYEE_RELATION_SISTER"),config("constants.EMPLOYEE_RELATION_UNCLE"),config("constants.EMPLOYEE_RELATION_AUNT")])->after('i_employee_id');
       	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config("constants.EMPLOYEE_RELATION_DETAILS_TABLE"), function (Blueprint $table) {
            //
        });
    }
}
