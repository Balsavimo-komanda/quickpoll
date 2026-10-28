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

Route::get('/polls/{poll}/results', [PollController::class, 'results'])
    ->name('polls.results');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/join', [PollController::class, 'joinForm'])->name('polls.join.form');
Route::post('/join', [PollController::class, 'join'])->middleware('throttle:10,1')->name('polls.join');
Route::get('/j/{code}', [PollController::class, 'joinByCode'])->name('polls.join.code');

Auth::routes();
