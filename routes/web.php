<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmiController;



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/loan-details', [EmiController::class, 'loanDetails'])->name('loan.details');
    Route::get('/process-emi', [EmiController::class, 'processEmi'])->name('emi.process');
    Route::get('/emi-details', [EmiController::class, 'showEmiDetails'])->name('emi.details');
});
