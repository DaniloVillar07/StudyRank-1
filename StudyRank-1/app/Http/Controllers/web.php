<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// ROTAS PÚBLICAS (sem autenticação)
// =============================================================================

// Raiz → redireciona para login ou dashboard dependendo do estado
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Login
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Cadastro
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// =============================================================================
// ROTAS PROTEGIDAS (exige login)
// Qualquer rota aqui sem autenticação → redireciona para 'login'
// =============================================================================

Route::middleware('auth')->group(function () {

    // Logout (POST para evitar logout acidental via link)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil do usuário
    Route::get('/perfil', [ProfileController::class, 'index'])->name('perfil');

    // Ranking semanal
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

    // Quizzes
    // GET  /quiz        → lista todos os quizzes
    // GET  /quiz/{id}   → exibe as perguntas de um quiz específico
    // POST /quiz/{id}   → recebe as respostas e processa o resultado
    Route::get( '/quiz',              [QuizController::class, 'index'])->name('quiz.index');
    Route::get( '/quiz/{quiz}',       [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/{quiz}/submit',[QuizController::class, 'submit'])->name('quiz.submit');

});
