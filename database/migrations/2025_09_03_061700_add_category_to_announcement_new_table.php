<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('announcement_new', function (Blueprint $table) {
            if (!Schema::hasColumn('announcement_new', 'category')) {
                $table->string('category')->default('Others')->index()->after('media');
            }
        });
    }

    public function down(): void
    {
        Schema::table('announcement_new', function (Blueprint $table) {
            if (Schema::hasColumn('announcement_new', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
