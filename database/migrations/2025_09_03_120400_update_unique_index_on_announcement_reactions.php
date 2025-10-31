<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('announcement_reactions', function (Blueprint $table) {
            // Drop previous unique if exists
            $table->dropUnique('uniq_ann_user_emoji');
            // Add new unique to enforce single reaction per user per announcement
            $table->unique(['announcement_id', 'user_id'], 'uniq_ann_user');
        });
    }

    public function down(): void
    {
        Schema::table('announcement_reactions', function (Blueprint $table) {
            $table->dropUnique('uniq_ann_user');
            $table->unique(['announcement_id', 'user_id', 'emoji'], 'uniq_ann_user_emoji');
        });
    }
};
