<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\ConversationController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\EmergencyController;
use App\Http\Controllers\Api\V1\ListenerController;
use App\Http\Controllers\Api\V1\MetaController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ProfessionalAgendaController;
use App\Http\Controllers\Api\V1\ProfessionalController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {

    // --- Public -----------------------------------------------------------
    Route::get('meta', MetaController::class)->name('meta');

    Route::prefix('auth')->name('auth.')->middleware('throttle:auth')->group(function () {
        Route::post('anonymous', [AuthController::class, 'anonymous'])->name('anonymous');
        Route::post('recover', [AuthController::class, 'recover'])->name('recover');
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
    });

    Route::get('professionals', [ProfessionalController::class, 'index'])->name('professionals.index');
    Route::get('professionals/{professional}', [ProfessionalController::class, 'show'])->name('professionals.show');
    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    Route::post('payments/webhooks/{provider}', [PaymentController::class, 'webhook'])->name('payments.webhook');
    Route::get('payments/mock/{reference}', [PaymentController::class, 'mock'])->name('payments.mock');

    // --- Authentifié --------------------------------------------------------
    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::get('me', [ProfileController::class, 'show'])->name('me.show');
        Route::patch('me', [ProfileController::class, 'update'])->name('me.update');
        Route::post('me/upgrade', [ProfileController::class, 'upgrade'])->name('me.upgrade');
        Route::delete('me', [ProfileController::class, 'destroy'])->name('me.destroy');

        // Niveau 1 — écoute anonyme
        Route::post('emergency', EmergencyController::class)->middleware('throttle:sos')->name('emergency');

        Route::prefix('conversations')->name('conversations.')->controller(ConversationController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->middleware('role:member')->name('store');
            Route::get('{conversation}', 'show')->name('show');
            Route::get('{conversation}/messages', 'messages')->name('messages');
            Route::post('{conversation}/messages', 'send')->middleware('throttle:messages')->name('send');
            Route::post('{conversation}/close', 'close')->name('close');
            Route::post('{conversation}/rating', 'rate')->name('rate');
            Route::post('{conversation}/reports', 'report')->name('report');
        });

        Route::prefix('listener')->name('listener.')->middleware('permission:conversations.listen')->controller(ListenerController::class)->group(function () {
            Route::patch('availability', 'availability')->name('availability');
            Route::get('queue', 'queue')->name('queue');
            Route::post('queue/{conversation}/take', 'take')->name('take');
        });

        // Niveau 2 — consultations
        Route::prefix('bookings')->name('bookings.')->controller(BookingController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->middleware('role:member')->name('store');
            Route::get('{booking}', 'show')->name('show');
            Route::post('{booking}/pay', 'pay')->name('pay');
            Route::post('{booking}/cancel', 'cancel')->name('cancel');
        });

        Route::prefix('pro')->name('pro.')->middleware('role:professional')->controller(ProfessionalAgendaController::class)->group(function () {
            Route::get('slots', 'slots')->name('slots.index');
            Route::post('slots', 'storeSlots')->name('slots.store');
            Route::delete('slots/{slot}', 'destroySlot')->name('slots.destroy');
            Route::get('bookings', 'bookings')->name('bookings.index');
            Route::post('bookings/{booking}/complete', 'complete')->name('bookings.complete');
        });

        // Niveau 3 — formations et abonnements
        Route::get('enrollments', [CourseController::class, 'enrollments'])->name('enrollments.index');
        Route::prefix('courses/{course}')->name('courses.')->controller(CourseController::class)->group(function () {
            Route::post('enroll', 'enroll')->name('enroll');
            Route::post('purchase', 'purchase')->name('purchase');
            Route::get('lessons/{lesson}', 'lesson')->name('lessons.show');
            Route::post('lessons/{lesson}/complete', 'completeLesson')->name('lessons.complete');
        });

        Route::get('subscription', [SubscriptionController::class, 'current'])->name('subscription.current');
        Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');

        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    });
});
