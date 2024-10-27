<?php

declare(strict_types=1);

use App\Http\Controllers\Head2HeadController;
use App\Http\Controllers\KimariteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RefreshController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/head2head', [Head2HeadController::class, 'view'])->name('head2head.view');
Route::get('/head2head/{id}', [Head2HeadController::class, 'head2headsForWrestler'])->name('head2head.wrestler');

Route::get('/', [KimariteController::class, 'show'])->name('kimarite.show');
Route::get('/kimarite-counts', [KimariteController::class, 'getCounts'])->name('kimarite.counts');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/refresh', [RefreshController::class, 'refresh'])->name('refresh');
    Route::post('/rebuild', [RefreshController::class, 'rebuild'])->name('rebuild');
    Route::post('/refresh-basho-percentages', [RefreshController::class, 'refreshBashoPercentages'])->name('refresh-basho-percentages');
});

require __DIR__.'/auth.php';
