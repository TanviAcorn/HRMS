<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalaryPfConsiderIntoComponenetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config('constants.SALARY_COMPONENTS_MASTER_TABLE'), function (Blueprint $table) {
    		$table->enum('e_consider_for_pf_calculation' , [ config('constants.SELECTION_YES') , config('constants.SELECTION_NO') ])->after('e_salary_components_frequence')->nullable();
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
