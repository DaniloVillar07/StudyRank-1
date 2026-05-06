<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        // 1. Ranking Geral por XP Total
        // Ajustado: 'streak' -> 'current_streak' / 'xp' -> 'xp_total'
        $users = User::select('id', 'name', 'avatar', 'xp_total', 'level', 'current_streak')
            ->orderBy('xp_total', 'desc')
            ->limit(10)
            ->get();

        // Adiciona posição no ranking
        $ranking = $users->map(function ($user, $index) {
            $user->rank = $index + 1;
            return $user;
        });

        // 2. Ranking Semanal Real
        // Buscamos os usuários e somamos o XP da tabela weekly_xps apenas para a semana atual
        $weeklyRanking = User::withSum(['weeklyXps as weekly_xp' => function ($query) {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }], 'xp_amount') // 'xp_amount' é o nome da coluna na sua tabela de XP semanal
            ->orderByDesc('weekly_xp')
            ->limit(10)
            ->get()
            ->map(function ($user, $index) {
                $user->rank = $index + 1;
                // Caso o usuário não tenha ganho XP na semana, garantimos que seja 0
                $user->weekly_xp = $user->weekly_xp ?? 0;
                return $user;
            });

        return view('ranking.index', compact('ranking', 'weeklyRanking'));
    }
}