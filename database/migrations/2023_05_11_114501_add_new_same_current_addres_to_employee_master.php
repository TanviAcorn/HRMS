<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewSameCurrentAddresToEmployeeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
           $table->enum('e_same_current_address', [config('constants.SELECTION_YES')])->after('i_probation_update_id')->nullable();
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
