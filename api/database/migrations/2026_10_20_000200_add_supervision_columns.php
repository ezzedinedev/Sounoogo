<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Supervision : délai de première réponse des écoutants et motif des alertes
 * (notamment celles proposées par l'IA, à confirmer par un humain).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->timestamp('first_response_at')->nullable()->after('assigned_at');
        });

        Schema::table('emergency_alerts', function (Blueprint $table) {
            $table->string('reason', 300)->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('emergency_alerts', fn (Blueprint $table) => $table->dropColumn('reason'));
        Schema::table('conversations', fn (Blueprint $table) => $table->dropColumn('first_response_at'));
    }
};
