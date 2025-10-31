<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_report', function (Blueprint $table) {
            $table->increments('i_id');
            $table->date('dt_report_date');
            $table->longText('v_employee_ids');
            $table->longText('v_subject');
            $table->longText('v_went_wrong')->nullable();
            $table->longText('v_actions_taken')->nullable();
            $table->longText('v_prevent_in_future')->nullable();
            $table->longText('v_comments')->nullable();
            $table->date('dt_close_date')->nullable();
            $table->longText('v_report_no');
            $table->integer('i_close_by_id')->nullable();
            $table->dateTime('dt_system_closed_at')->nullable();
            $table->tinyInteger('t_is_active')->default('1');
            $table->tinyInteger('t_is_deleted')->default('0');
            $table->integer('i_created_id');
            $table->dateTime('dt_created_at');
            $table->integer('i_updated_id')->nullable();
            $table->dateTime('dt_updated_at')->nullable();
            $table->integer('i_deleted_id')->nullable();
            $table->dateTime('dt_deleted_at')->nullable();
            $table->ipAddress('v_ip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_report');
    }
}
