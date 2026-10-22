<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PollController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/polls', [PollController::class, 'store'])->name('polls.store')
    ->middleware('throttle:5,1'); // Max 5 create poll attemps per minute per IP.

Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote')
    ->middleware('throttle:5,1'); // Max 5 vote attempts per minute per IP.

Route::get('/polls/create', [PollController::class, 'create'])->name('polls.create');

Route::get('/polls/{poll}', [PollController::class, 'show'])->name('polls.show');

Route::get('/', [HomeController::class, 'index'])->name('home');

Auth::routes();
