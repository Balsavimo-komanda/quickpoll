<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PollController;
use Illuminate\Support\Facades\Route;

Route::get('/polls/create', [PollController::class, 'create'])->name('polls.create');
Route::post('/polls', [PollController::class, 'store'])->name('polls.store');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/polls/{poll}', [PollController::class, 'show'])->name('polls.show');
Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote');
