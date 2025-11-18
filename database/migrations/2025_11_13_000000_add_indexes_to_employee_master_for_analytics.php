<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToEmployeeMasterForAnalytics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.EMPLOYEE_MASTER_TABLE'), function (Blueprint $table) {
            // Add index on employment status for filtering queries
            $table->index('e_employment_status', 'idx_employment_status');
            
            // Add index on joining date for additions/attritions queries
            $table->index('dt_joining_date', 'idx_joining_date');
            
            // Add index on relieving date for attritions queries
            $table->index('dt_relieving_date', 'idx_relieving_date');
            
            // Add composite index on is_deleted and employment_status for optimized filtering
            $table->index(['t_is_deleted', 'e_employment_status'], 'idx_deleted_status');
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
            // Drop indexes in reverse order
            $table->dropIndex('idx_deleted_status');
            $table->dropIndex('idx_relieving_date');
            $table->dropIndex('idx_joining_date');
            $table->dropIndex('idx_employment_status');
        });
    }
}
