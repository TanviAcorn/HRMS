<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAfternoonShift extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	//SHIFT_MASTER_TABLE
        //
    	Schema::table(config('constants.SHIFT_MASTER_TABLE'), function (Blueprint $table) {
    		//$table->enum('e_shift_type',[config('constants.MORNING_SHIFT'),config('constants.NIGHT_SHIFT'),config('constants.AFTERNOON_SHIFT')])->default(config('constants.MORNING_SHIFT'))->change();
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
