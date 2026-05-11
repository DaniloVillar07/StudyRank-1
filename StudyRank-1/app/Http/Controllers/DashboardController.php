<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Quiz;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard principal do usuário.
     *
     * Variáveis passadas para a view dashboard.blade.php:
     *   $user           → usuário com badges e quizProgress carregados
     *   $quizzes        → quizzes ativos com progresso do usuário injetado
     *   $allBadges      → todas as badges (para exibir as bloqueadas também)
     *   $completedCount → quantos quizzes o usuário já concluiu
     *   $xp_needed      → XP para chegar ao próximo nível (sempre 100)
     */
    public function index()
    {
        $user = auth()->user();

        // Carregamos as badges e o progresso de quizzes do usuário em uma
        // única query extra cada (eager loading), evitando o problema N+1.
        $user->load(['badges', 'quizProgress']);

        // Buscamos todos os quizzes ativos e, para cada um, carregamos
        // apenas o progresso do usuário logado (não de todos os usuários).
        $quizzes = Quiz::where('is_active', true)
            ->with(['users' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get()
            // Adicionamos um atributo de conveniência: o pivot do usuário (ou null)
            ->map(function (Quiz $quiz) {
                $quiz->user_progress = $quiz->users->first()?->pivot;
                return $quiz;
            });

        // Conta quizzes concluídos usando a coleção já carregada (sem nova query)
        $completedCount = $quizzes->filter(
            fn($q) => $q->user_progress?->completed === true
        )->count();

        // Todas as badges para exibir o grid (incluindo as bloqueadas)
        $allBadges = Badge::all();

        // Fórmula: 100 XP por nível
        // O XP necessário é sempre 100 (do XP atual dentro do nível atual ao próximo)
        $xp_needed = 100;

        return view('dashboard', compact(
            'user',
            'quizzes',
            'allBadges',
            'completedCount',
            'xp_needed'
        ));
    }
}
