<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarksColumnToIncidentReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.INCIDENT_REPORT_TABLE'), function (Blueprint $table) {
            //
        	$table->longText('v_remarks')->nullable()->after('v_comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.INCIDENT_REPORT_TABLE'), function (Blueprint $table) {
            //
        });
    }
}
