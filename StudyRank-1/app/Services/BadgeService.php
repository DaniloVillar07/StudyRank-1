<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;

class BadgeService
{
    /**
     * Verifica e atribui novas medalhas ao usuário.
     */
    public function checkBadges(User $user): void
    {
        // Buscamos todas as medalhas que o usuário AINDA não possui
        $availableBadges = Badge::whereDoesntHave('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        /** @var Badge $badge */
        foreach ($availableBadges as $badge) {
            if ($this->shouldAward($user, $badge)) {
                $user->badges()->syncWithoutDetaching([
                    $badge->id => ['earned_at' => now()]
                ]);
            }
        }
    }

    /**
     * Lógica de validação baseada no tipo da Badge.
     *
     * CORREÇÃO: 'streak_count' → 'current_streak'
     * O campo no model/migration é 'current_streak', não 'streak_count'.
     */
    private function shouldAward(User $user, Badge $badge): bool
    {
        return match ($badge->type) {
            // XP acumulado total do usuário
            'xp'      => $user->xp_total >= $badge->target_value,

            // Quantidade de quizzes concluídos
            'quizzes' => $user->quizzes()
                              ->wherePivot('completed', true)
                              ->count() >= $badge->target_value,

            // CORRIGIDO: era $user->streak_count (não existe)
            //            agora é $user->current_streak (campo real da tabela)
            'streak'  => $user->current_streak >= $badge->target_value,

            // Badges de ranking são processadas pelo comando semanal (RankingSeeder)
            default   => false,
        };
    }
}
