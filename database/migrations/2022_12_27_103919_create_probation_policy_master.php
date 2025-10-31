<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbationPolicyMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.PROBATION_POLICY_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->longText('v_probation_policy_name');
            $table->longText('v_probation_policy_description')->nullable();
            $table->longText('v_probation_period_duration');
            $table->enum('e_months_weeks_days', [config('constants.MONTH_DURATION'),config('constants.WEEKS_DURATION'),config('constants.DAYS_DURATION')]);
            $table->enum('e_record_status', [config('constants.PROBATION_POLICY'),config('constants.NOTICE_PERIOD_POLICY')]);
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
        Schema::dropIfExists(config('constants.PROBATION_POLICY_MASTER_TABLE'));
    }
}
