<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ProfileController;

// 1. Rotas Públicas (Exemplo de Autenticação Manual)
Route::get('/login', function () { return view('auth.login'); })->name('login');
// Adicione aqui suas rotas de POST para login/logout

// 2. Rotas Protegidas (O Coração do Game)
Route::middleware(['auth'])->group(function () {

    // Dashboard Principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Quizzes
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [QuizController::class, 'index'])->name('index'); // Lista todos
        Route::get('/{quiz}', [QuizController::class, 'show'])->name('show'); // Abre um específico
        Route::post('/{quiz}/submit', [QuizController::class, 'submit'])->name('submit'); // Envia respostas
    });

    // Ranking / Leaderboard
    Route::get('/ranking', [LeaderboardController::class, 'index'])->name('ranking.index');

    // Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');

});

// Rota raiz redirecionando para o dashboard ou login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});
