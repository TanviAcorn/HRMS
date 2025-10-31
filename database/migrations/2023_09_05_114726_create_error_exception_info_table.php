<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorExceptionInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.ERROR_EXCEPTION_INFO_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->longText('v_server_info')->nullable();
            $table->longText('v_request_info')->nullable();
            $table->longText('v_session_info')->nullable();
            $table->longText('v_error_info')->nullable();
            $table->longText('v_error_message')->nullable();
            $table->integer('i_created_id');
    		$table->dateTime('dt_created_at');
    		$table->integer('i_updated_id')->nullable();;
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
        Schema::dropIfExists(config('constants.ERROR_EXCEPTION_INFO_TABLE'));
    }
}
