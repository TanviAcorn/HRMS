<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.EMAIL_HISTORY_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_login_id');
            $table->integer('i_related_record_id')->nullable();
            $table->longText('v_event');
        	$table->longText('v_receiver_email');
        	$table->longText('v_subject');
        	$table->longText('v_mail_content');
        	$table->longText('v_response')->nullable();
        	$table->enum('e_status',[config('constants.PENDING_STATUS'),config('constants.SUCCESS_STATUS'),config('constants.FAILED_STATUS'),config('constants.PROCESSING_STATUS')])->default( config('constants.PENDING_STATUS') );
        	$table->enum('e_notification_status',[config('constants.PENDING_STATUS'),config('constants.SUCCESS_STATUS'),config('constants.FAILED_STATUS'),config('constants.PROCESSING_STATUS')])->default( config('constants.PENDING_STATUS') );
        	$table->tinyInteger('t_read_notification')->default('0');
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
        Schema::dropIfExists(config('constants.EMAIL_HISTORY_TABLE'));
    }
}
