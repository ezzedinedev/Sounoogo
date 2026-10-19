<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contenus : exercices guidés, guides « aider un proche » et annuaire des ressources de proximité.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('summary', 300);
            $table->string('category', 20)->index();
            $table->unsignedSmallInteger('duration_minutes')->default(3);
            // Étapes guidées : [{ text, seconds }]
            $table->json('steps')->nullable();
            // Respiration rythmée : { inhale, hold, exhale, rest, cycles }
            $table->json('breathing')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('language', 5)->default('fr');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('summary', 300);
            $table->longText('body');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('support_resources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category', 30)->index();
            $table->string('country', 2)->default('BF');
            $table->string('city', 80)->index();
            $table->string('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('website')->nullable();
            $table->string('description', 500)->nullable();
            $table->string('opening_hours', 160)->nullable();
            $table->boolean('is_free')->default(false);
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_resources');
        Schema::dropIfExists('guides');
        Schema::dropIfExists('exercises');
    }
};
