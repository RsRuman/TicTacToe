<?php

use App\Http\Controllers\GameController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/tic-tac-toe', [GameController::class, 'index'])->name('game.index');
    Route::post('/tic-tac-toe', [GameController::class, 'create'])->name('game.create');
    Route::get('/tic-tac-toe/{id}', [GameController::class, 'current_game'])->name('game.current');
    Route::get('/tic-tac-toe/game/{id}', [GameController::class, 'show'])->name('game.show');
    Route::post('/tic-tac-toe/game/{id}', [GameController::class, 'status_update'])->name('game.status.update');
    Route::post('/tic-tac-toe/{id}', [GameController::class, 'board'])->name('game.board');
    Route::post('/tic-tac-toe/{id}', [GameController::class, 'set_house'])->name('game.house');
});
