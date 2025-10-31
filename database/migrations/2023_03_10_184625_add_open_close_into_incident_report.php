<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpenCloseIntoIncidentReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.INCIDENT_REPORT_TABLE'), function (Blueprint $table) {
    		$table->enum('e_status' , [config('constants.OPEN') , config('constants.CLOSE')])->default(config('constants.OPEN'))->after('v_subject');
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
