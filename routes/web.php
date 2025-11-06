<?php

use App\Http\Controllers\Auth\SendOtpController;
use App\Http\Controllers\Auth\VerifyOtpController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::post('login/otp', SendOtpController::class)->name('login.otp');
Route::post('login/verify', VerifyOtpController::class)->name('login.verify');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
