<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Route::get('/', function () {
    //     return view('dashboard');
    // })->name('dashboard');
    Route::get('/', [UrlController::class, 'showAddUrlForm'])->name('dashboard');

    Route::post('/shorten-url', [UrlController::class, 'shortenUrl'])->name('shorten-url');
    Route::post('/deactivate-url', [UrlController::class, 'deactivateUrl'])->name('deactivate-url');
    Route::post('/activate-url', [UrlController::class, 'activateUrl'])->name('activate-url');
    Route::post('/delete-url', [UrlController::class, 'destroy'])->name('delete-url');
    Route::post('/edit-url', [UrlController::class, 'editUrl'])->name('edit-url');
    Route::get('/plans', [UrlController::class, 'showPlans'])->name('plans');
    Route::post('/change-plan', [UrlController::class, 'changePlan'])->name('change-plan');

    
});

Route::get('/u/{id}', [UrlController::class, 'redirectToOriginalUrl'])->name('u');

