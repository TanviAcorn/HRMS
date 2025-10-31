<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEducationCgpaIntoEmployeeMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
    		$table->longText('v_education')->after('e_same_current_address')->nullable();
    		$table->longText('v_cgpa')->after('v_education')->nullable();
    		$table->date('dt_pf_expiry_date')->after('v_cgpa')->nullable();
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
