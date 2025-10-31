<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToEmployeeRelationDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config("constants.EMPLOYEE_RELATION_DETAILS_TABLE"), function (Blueprint $table) {
        	$table->dropColumn('e_employee_relation');
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
