<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config("constants.SETTING_TABLE"), function (Blueprint $table) {
            $table->increments('i_id');
            $table->longText('v_primary_mobile_no')->nullable();
            $table->longText('v_secondary_mobile_no')->nullable();
            $table->longText('v_other_mobile_no')->nullable();
            $table->longText('v_whatsapp_number')->nullable();
            $table->enum('e_whatsapp_position',[config('constants.LEFT'),config('constants.RIGHT')])->default(config('constants.LEFT'));
            $table->longText('v_email')->nullable();
            $table->longText('v_working_hours')->nullable();
            $table->longText('v_working_days')->nullable();
            $table->longText('v_google_map')->nullable();
            $table->longText('v_short_address')->nullable();
            $table->longText('v_address')->nullable();
            $table->longText('v_facebook_link')->nullable();
            $table->longText('v_instagram_link')->nullable();
            $table->longText('v_youtube_link')->nullable();
            $table->longText('v_linkedin_link')->nullable();
            $table->longText('v_twitter_link')->nullable();
            $table->longText('v_site_title')->nullable();
            $table->longText('v_site_keywords')->nullable();
            $table->longText('v_about_short_description')->nullable();
            $table->longText('v_site_description')->nullable();
            $table->longText('v_time_off_policy')->nullable();
            $table->longText('v_leave_policy')->nullable();
            $table->longText('v_meta_author')->nullable();
            $table->longText('v_powered_by')->nullable();
            $table->longText('v_powered_by_link')->nullable();
            $table->longText('v_site_name')->nullable();
            $table->decimal('d_version')->nullable();
            $table->longText('v_default_cc_mail')->nullable();
            $table->longText('v_contact_receive_mail')->nullable();
            $table->longText('v_send_email_protocol')->nullable();
            $table->longText('v_send_email_host')->nullable();
            $table->longText('i_send_email_port')->nullable();
            $table->longText('v_send_email_user')->nullable();
            $table->longText('v_send_email_password')->nullable();
            $table->longText('v_website_logo')->nullable();
            $table->longText('v_website_footer_logo')->nullable();
            $table->longText('v_website_fav_icon')->nullable();
            $table->longText('v_website_og_icon')->nullable();
            $table->tinyInteger('t_contact_settings_tab')->default('1');
            $table->tinyInteger('t_social_links_tab')->default('1');
            $table->tinyInteger('t_smtp_connection_tab')->default('1');
            $table->tinyInteger('t_site_info_tab')->default('1');
            $table->tinyInteger('t_logo_settings_tab')->default('1');
            $table->tinyInteger('t_send_email')->default('1');
            $table->dateTime('dt_last_updated_at')->nullable();
            $table->longText('v_address_hindi')->nullable();
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
        Schema::dropIfExists(config("constants.SETTING_TABLE"));
    }
}
