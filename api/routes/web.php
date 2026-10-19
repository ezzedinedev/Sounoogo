<?php

use App\Http\Controllers\Site\SiteController;
use Illuminate\Support\Facades\Route;

// --- Site vitrine -----------------------------------------------------------
Route::controller(SiteController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('professionnels', 'professionals')->name('professionals');
    Route::get('formations', 'courses')->name('courses');
    Route::get('urgence', 'emergency')->name('emergency');
    Route::get('confidentialite', 'privacy')->name('privacy');
    Route::get('paiement/{result}', 'paymentResult')->whereIn('result', ['succes', 'echec'])->name('payment.result');
});

require __DIR__.'/backoffice.php';
