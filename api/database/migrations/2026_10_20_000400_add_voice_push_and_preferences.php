<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Messages vocaux (fichiers chiffrés hors base), écoutant préféré, appareils pour les
 * notifications push et rappels de consultation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('preferred_listener_id')->nullable()->after('is_available')->constrained('users')->nullOnDelete();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->string('audio_path')->nullable()->after('body');
            $table->string('audio_mime', 40)->nullable()->after('audio_path');
            $table->unsignedSmallInteger('audio_duration')->nullable()->after('audio_mime');
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token')->unique();
            $table->string('platform', 10);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('reminded_at')->nullable()->after('confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', fn (Blueprint $table) => $table->dropColumn('reminded_at'));
        Schema::dropIfExists('device_tokens');
        Schema::table('messages', fn (Blueprint $table) => $table->dropColumn(['audio_path', 'audio_mime', 'audio_duration']));
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('preferred_listener_id');
        });
    }
};
