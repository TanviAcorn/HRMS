<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationColumnIntoEmailHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::table(config("constants.EMAIL_HISTORY_TABLE"), function (Blueprint $table) {
    		$table->longText('v_notification_title')->after('e_notification_status')->nullable();
    		$table->longText('v_notification_body')->after('v_notification_title')->nullable();
    		$table->longText('v_notification_response')->after('v_notification_body')->nullable();
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
