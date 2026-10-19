<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rappel des consultations, une heure avant.
Schedule::command('bookings:send-reminders')->everyFiveMinutes()->withoutOverlapping();

// Sauvegardes : base de données et fichiers déposés, archive chiffrée si BACKUP_ARCHIVE_PASSWORD est défini.
Schedule::command('backup:clean')->dailyAt('01:00')->onOneServer();
Schedule::command('backup:run')->dailyAt('01:30')->onOneServer();
Schedule::command('backup:monitor')->dailyAt('06:00')->onOneServer();
