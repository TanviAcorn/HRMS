<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeInfoColumnIntoAmendmentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.AMENDMENT_HISTOY_TABLE'), function (Blueprint $table) {
    		$table->longText('v_change_info')->after('v_current_info')->nullable();
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('constants.AMENDMENT_HISTOY_TABLE'));
    }
}
