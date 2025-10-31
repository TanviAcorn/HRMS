<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSettingsColumnToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table(config("constants.SETTING_TABLE"), function (Blueprint $table) {
        	$table->dropColumn('dt_last_updated_at');
        	$table->dropColumn('v_address_hindi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::table(config("constants.SETTING_TABLE"), function (Blueprint $table) {
            //
        });
    }
}
