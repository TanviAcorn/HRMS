<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('announcement_reactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('announcement_id');
            $table->unsignedBigInteger('user_id');
            $table->string('emoji', 16);
            $table->timestamps();

            // Indexes
            $table->index(['announcement_id']);
            $table->index(['user_id']);
            $table->unique(['announcement_id', 'user_id', 'emoji'], 'uniq_ann_user_emoji');
            // Note: intentionally avoiding foreign key constraints to match existing schema conventions
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_reactions');
    }
};
