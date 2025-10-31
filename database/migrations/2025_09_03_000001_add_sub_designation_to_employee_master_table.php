<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubDesignationToEmployeeMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
            if (!Schema::hasColumn(config('constants.EMPLOYEE_MASTER_TABLE'), 'i_sub_designation_id')) {
                $table->integer('i_sub_designation_id')->nullable()->after('i_designation_id');
            }
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
            if (Schema::hasColumn(config('constants.EMPLOYEE_MASTER_TABLE'), 'i_sub_designation_id')) {
                $table->dropColumn('i_sub_designation_id');
            }
        });
    }
}
