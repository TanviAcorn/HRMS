<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMediaToAnnouncementNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('announcement_new', function (Blueprint $table) {
        $table->text('media')->nullable()->after('content');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
   public function down()
{
    Schema::table('announcement_new', function (Blueprint $table) {
        $table->dropColumn('media');
    });
}
}
