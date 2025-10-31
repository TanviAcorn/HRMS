<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignEmployeeIdsToRolePermision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.ROLE_PERMISSION_TABLE'), function (Blueprint $table) {
            $table->text('v_assign_employees')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('constants.ROLE_PERMISSION_TABLE'), function (Blueprint $table) {
            $table->dropColumn('v_assign_employees');
        });
    }
}
