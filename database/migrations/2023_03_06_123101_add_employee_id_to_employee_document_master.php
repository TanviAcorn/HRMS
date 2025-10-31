<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeIdToEmployeeDocumentMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.EMPLOYEE_DOCUMENT_MASTER_DETAILS_TABLE'), function (Blueprint $table) {
        	$table->integer('i_employee_id')->after('i_document_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.EMPLOYEE_DOCUMENT_MASTER_DETAILS_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
