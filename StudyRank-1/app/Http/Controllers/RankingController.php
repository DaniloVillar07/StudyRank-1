<?php

namespace App\Http\Controllers;

use App\Models\WeeklyXp;
use Carbon\Carbon;

class RankingController extends Controller
{
    /**
     * Exibe o ranking semanal.
     *
     * Variáveis passadas para ranking/index.blade.php:
     *   $top10        → top 10 registros de WeeklyXp desta semana com user
     *   $myWeeklyXp   → registro do usuário logado nesta semana (ou null)
     *   $maxXp        → XP do 1º colocado (para calcular proporção das barras)
     *   $quizzesDone  → quizzes concluídos pelo usuário (para barra do desafio)
     */
    public function index()
    {
        $user = auth()->user();

        // Segunda-feira desta semana — mesmo critério usado no XpService
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

        // Top 10 da semana, do maior para o menor XP
        // Carregamos o user em cada registro para exibir nickname e avatar
        $top10 = WeeklyXp::where('week_start', $weekStart)
            ->orderBy('xp_earned_this_week', 'desc')
            ->with('user')   // eager loading — evita N+1
            ->limit(10)
            ->get();

        // Registro do usuário logado nesta semana (pode ser null se não jogou)
        $myWeeklyXp = WeeklyXp::where('week_start', $weekStart)
            ->where('user_id', $user->id)
            ->first();

        // XP do líder — usado para calcular as barras de progresso (%)
        // Se não há ninguém no ranking, evitamos divisão por zero
        $maxXp = $top10->max('xp_earned_this_week') ?? 1;

        // Quizzes concluídos pelo usuário (para a barra do desafio semanal)
        // Usamos a relação quizProgress já no User sem nova query ao banco
        $user->load('quizProgress');
        $quizzesDone = $user->quizProgress->where('completed', true)->count();

        return view('ranking.index', compact(
            'top10',
            'myWeeklyXp',
            'maxXp',
            'quizzesDone'
        ));
    }
}
