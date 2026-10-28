<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PollController;

// Authentication.
Auth::routes();

// Home page.
Route::get('/', [HomeController::class, 'index'])->name('home');

// Show the form to enter a poll code manually.
Route::get('/join', [PollController::class, 'joinForm'])->name('polls.join.form');

// Process the join code submission.
// Max 10 join attempts per minute per IP.
Route::post('/join', [PollController::class, 'join'])->name('polls.join')->middleware('throttle:10,1');

// Direct join via URL code parameter.
Route::get('/join/{code}', [PollController::class, 'joinByCode'])->name('polls.join.code');

// Show the form to create a new poll.
Route::get('/polls/create', [PollController::class, 'create'])->name('polls.create');

// Store the newly created poll.
// Max 5 create poll attempts per minute per IP.
Route::post('/polls', [PollController::class, 'store'])->name('polls.store')->middleware('throttle:5,1');

// View a specific poll.
Route::get('/polls/{poll}', [PollController::class, 'show'])->name('polls.show');

// View results for a specific poll.
Route::get('/polls/{poll}/results', [PollController::class, 'results'])->name('polls.results');

// Submit a vote for a poll.
// Max 5 vote attempts per minute per IP.
Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote')->middleware('throttle:5,1');
