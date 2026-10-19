<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Niveau 2 — consultations avec des professionnels partenaires, et paiements.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professional_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title', 80);
            $table->string('specialty', 80);
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->json('languages')->nullable();
            $table->json('modes')->nullable();
            $table->unsignedInteger('session_price');
            $table->unsignedSmallInteger('session_minutes')->default(50);
            $table->string('city', 60)->nullable();
            $table->string('license_number', 60)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at');
            $table->boolean('is_booked')->default(false);
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('professional_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('availability_slot_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mode', 15);
            $table->string('status', 20)->default('pending_payment')->index();
            $table->unsignedInteger('price');
            $table->boolean('keep_anonymous')->default(true);
            $table->text('member_note')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('payable');
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('XOF');
            $table->string('provider', 20);
            $table->string('status', 20)->default('pending')->index();
            $table->string('reference', 40)->unique();
            $table->string('provider_reference')->nullable()->index();
            $table->text('checkout_url')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('availability_slots');
        Schema::dropIfExists('professional_profiles');
    }
};
