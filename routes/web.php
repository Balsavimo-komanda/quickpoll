<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PollController;

Route::get('/polls/create', [PollController::class, 'create'])->name('polls.create');
Route::post('/polls', [PollController::class, 'store'])->name('polls.store');
Route::get('/', [HomeController::class, 'index'])->name('home');