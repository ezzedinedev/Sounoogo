<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Progression dans les formations (badges, certificats), salles de téléconsultation
 * et versements aux professionnels.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('badge', 30);
            $table->timestamp('awarded_at');
            $table->unique(['user_id', 'badge']);
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('holder_name', 80);
            $table->timestamp('issued_at');
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('video_room', 64)->nullable()->unique()->after('member_note');
            // Part du professionnel, figée à la fin de la consultation (prix moins la commission en vigueur).
            $table->unsignedInteger('professional_share')->nullable()->after('price');
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->string('status', 20)->default('requested')->index();
            $table->string('phone', 20);
            $table->string('reference', 60)->nullable();
            $table->string('note')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['video_room']);
            $table->dropColumn(['video_room', 'professional_share']);
        });
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('user_badges');
    }
};
