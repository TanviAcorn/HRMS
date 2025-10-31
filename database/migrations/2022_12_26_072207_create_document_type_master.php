<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTypeMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create(config('constants.DOCUMENT_TYPE_MASTER_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_document_folder_id');
            $table->longText('v_document_type');
            $table->longText('v_document_description')->nullable();
            $table->enum('e_multiple_allowed_employee', [config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->default(config('constants.SELECTION_NO'));
            $table->enum('e_visible_to_employee', [config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->default(config('constants.SELECTION_NO'));
            $table->enum('e_modifiable_employee', [config('constants.SELECTION_YES'),config('constants.SELECTION_NO')])->default(config('constants.SELECTION_NO'));
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
        Schema::dropIfExists(config('constants.DOCUMENT_TYPE_MASTER_TABLE'));
    }
}
