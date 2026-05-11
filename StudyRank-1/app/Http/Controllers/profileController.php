<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\WeeklyXp;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Exibe o perfil do usuário logado.
     *
     * Variáveis passadas para perfil.blade.php:
     *   $user         → usuário com badges e quizProgress (com quiz) carregados
     *   $allBadges    → todas as badges do sistema
     *   $xp_needed    → XP para o próximo nível
     *   $rankPosition → posição no ranking desta semana (ou 'Não ranqueado')
     */
    public function index()
    {
        $user = auth()->user();

        // Carregamos badges, progresso de quizzes e o quiz de cada progresso
        // tudo em 3 queries extras — sem N+1.
        $user->load([
            'badges',
            // quizProgress com o quiz relacionado (para exibir o título no histórico)
            'quizProgress.quiz',
        ]);

        // Todas as badges — para mostrar o grid completo (bloqueadas e conquistadas)
        $allBadges = Badge::all();

        // Fórmula de nível: 100 XP por nível
        $xp_needed = 100;

        // Posição do usuário no ranking semanal atual
        $weekStart    = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $myWeeklyXp   = WeeklyXp::where('week_start', $weekStart)
            ->where('user_id', $user->id)
            ->first();

        // Se o usuário tem posição registrada, exibimos o número; senão, texto
        $rankPosition = $myWeeklyXp?->rank_position ?? 'Não ranqueado';

        return view('perfil', compact(
            'user',
            'allBadges',
            'xp_needed',
            'rankPosition'
        ));
    }
}
