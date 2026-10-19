<?php

use App\Http\Controllers\Backoffice;
use Illuminate\Support\Facades\Route;

// --- Back-office (session web) ---------------------------------------------
Route::prefix('admin')->name('backoffice.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('connexion', [Backoffice\Auth\LoginController::class, 'create'])->name('login');
        Route::post('connexion', [Backoffice\Auth\LoginController::class, 'store'])->middleware('throttle:auth')->name('login.store');
    });

    Route::middleware(['auth', 'active', 'permission:backoffice.access'])->group(function () {
        Route::post('deconnexion', [Backoffice\Auth\LoginController::class, 'destroy'])->name('logout');
        Route::get('/', Backoffice\DashboardController::class)->name('dashboard');

        // Écoute (écoutants et superviseurs)
        Route::middleware('permission:conversations.listen')->prefix('ecoute')->name('listening.')->controller(Backoffice\ListeningController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('disponibilite', 'availability')->name('availability');
            Route::get('{conversation}', 'show')->name('show');
            Route::post('{conversation}/prendre', 'take')->name('take');
            Route::post('{conversation}/messages', 'send')->name('send');
            Route::post('{conversation}/cloturer', 'close')->name('close');
            Route::get('{conversation}/messages/{message}/audio', 'audio')->name('audio');
        });

        // Copilote de l'écoutant (IA, si activée)
        Route::middleware(['permission:conversations.listen', 'throttle:copilot'])->prefix('ecoute/{conversation}/copilote')->name('copilot.')->controller(Backoffice\CopilotController::class)->group(function () {
            Route::post('suggestions', 'suggestions')->name('suggestions');
            Route::post('resume', 'summary')->name('summary');
        });

        // Poste de supervision temps réel
        Route::middleware('permission:alerts.manage')->prefix('supervision')->name('supervision.')->controller(Backoffice\SupervisionController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('direct', 'live')->name('live');
        });

        // Supervision
        Route::middleware('permission:alerts.manage')->prefix('alertes')->name('alerts.')->controller(Backoffice\AlertController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('{alert}/prise-en-charge', 'acknowledge')->name('acknowledge');
            Route::post('{alert}/resolution', 'resolve')->name('resolve');
        });

        Route::middleware('permission:reports.moderate')->prefix('signalements')->name('reports.')->controller(Backoffice\ReportController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('{report}/traitement', 'resolve')->name('resolve');
        });

        // Espace professionnel
        Route::middleware('role:professional')->prefix('agenda')->name('agenda.')->controller(Backoffice\AgendaController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('creneaux', 'storeSlot')->name('slots.store');
            Route::post('creneaux/serie', 'storeSeries')->name('slots.series');
            Route::delete('creneaux/{slot}', 'destroySlot')->name('slots.destroy');
            Route::post('consultations/{booking}/terminer', 'complete')->name('bookings.complete');
            Route::get('consultations/{booking}/visio', 'video')->name('bookings.video');
        });
        Route::middleware('role:professional')->prefix('revenus')->name('earnings.')->controller(Backoffice\EarningsController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('versements', 'store')->middleware('throttle:6,1')->name('payouts.store');
        });

        // Administration
        Route::middleware('permission:users.manage')->group(function () {
            Route::resource('utilisateurs', Backoffice\UserController::class)->only(['index', 'create', 'store'])->parameters(['utilisateurs' => 'user'])->names('users');
        });
        Route::post('utilisateurs/{user}/suspension', [Backoffice\UserController::class, 'toggleSuspension'])->middleware('permission:users.suspend')->name('users.suspension');

        Route::middleware('permission:professionals.manage')->group(function () {
            Route::resource('professionnels', Backoffice\ProfessionalController::class)->except(['show', 'destroy'])->parameters(['professionnels' => 'professional'])->names('professionals');
            Route::post('professionnels/{professional}/verification', [Backoffice\ProfessionalController::class, 'toggleVerification'])->name('professionals.verification');
        });

        Route::get('reservations', Backoffice\BookingController::class)->middleware('permission:bookings.manage')->name('bookings.index');
        Route::get('paiements', Backoffice\PaymentController::class)->middleware('permission:payments.view')->name('payments.index');
        Route::middleware('permission:payouts.manage')->prefix('versements')->name('payouts.')->controller(Backoffice\PayoutController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('{payout}', 'update')->name('update');
        });

        Route::middleware('permission:courses.manage')->group(function () {
            Route::resource('formations', Backoffice\CourseController::class)->except(['show'])->parameters(['formations' => 'course'])->names('courses');
            Route::post('formations/{course}/lecons', [Backoffice\LessonController::class, 'store'])->name('courses.lessons.store');
            Route::put('formations/{course}/lecons/{lesson}', [Backoffice\LessonController::class, 'update'])->scopeBindings()->name('courses.lessons.update');
            Route::delete('formations/{course}/lecons/{lesson}', [Backoffice\LessonController::class, 'destroy'])->scopeBindings()->name('courses.lessons.destroy');
        });

        Route::middleware('permission:content.manage')->group(function () {
            Route::resource('exercices', Backoffice\ExerciseController::class)->except(['show'])->parameters(['exercices' => 'exercise'])->names('exercises');
            Route::resource('guides', Backoffice\GuideController::class)->except(['show'])->names('guides');
            Route::resource('ressources', Backoffice\SupportResourceController::class)->except(['show'])->parameters(['ressources' => 'resource'])->names('resources');
        });

        Route::middleware('permission:plans.manage')->group(function () {
            Route::get('offres', [Backoffice\PlanController::class, 'index'])->name('plans.index');
            Route::put('offres/{plan}', [Backoffice\PlanController::class, 'update'])->name('plans.update');
        });

        Route::middleware('permission:emergency-contacts.manage')->prefix('numeros-urgence')->name('emergency-contacts.')->controller(Backoffice\EmergencyContactController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('{contact}', 'update')->name('update');
            Route::delete('{contact}', 'destroy')->name('destroy');
        });

        Route::middleware('permission:impact.view')->prefix('impact')->name('impact.')->controller(Backoffice\ImpactController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('export.csv', 'csv')->name('csv');
            Route::get('export.pdf', 'pdf')->name('pdf');
        });

        Route::get('journal', Backoffice\ActivityController::class)->middleware('permission:activity.view')->name('activity.index');
    });
});

// Nom attendu par le middleware "auth" de Laravel.
Route::redirect('login', '/admin/connexion')->name('login');
