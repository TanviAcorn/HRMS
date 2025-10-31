<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
class CreateAnnouncementNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_new', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->string('media')->nullable();
    $table->unsignedBigInteger('created_by');
    $table->timestamps();
});
 
    }
 
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement_new');
    }
}