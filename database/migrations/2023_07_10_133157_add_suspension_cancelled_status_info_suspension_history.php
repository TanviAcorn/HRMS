<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuspensionCancelledStatusInfoSuspensionHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.SUSPEND_HISTORY_TABLE'), function (Blueprint $table) {
    		$table->tinyInteger('t_is_cancelled')->default(0)->after('v_suspend_reason');
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
