<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->string('speaker_name')->nullable()->after('cover_photo_path');
            $table->text('speaker_bio')->nullable()->after('speaker_name');
            $table->string('speaker_photo_path', 2048)->nullable()->after('speaker_bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn(['speaker_name', 'speaker_bio', 'speaker_photo_path']);
        });
    }
};
