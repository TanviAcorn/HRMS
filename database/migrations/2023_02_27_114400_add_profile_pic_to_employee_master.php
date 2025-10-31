<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfilePicToEmployeeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
           $table->longText('v_profile_pic')->after('e_employment_status')->nullable();
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
