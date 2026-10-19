<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bien-être : plan de sécurité personnel et journal d'humeur.
 * Les contenus écrits par le membre sont chiffrés au repos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safety_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('mood_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('recorded_on');
            $table->unsignedTinyInteger('mood');
            $table->json('feelings')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'recorded_on']);
            $table->index('recorded_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_entries');
        Schema::dropIfExists('safety_plans');
    }
};
