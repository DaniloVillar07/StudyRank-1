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
        // 1. Buscamos todas as medalhas que o usuário AINDA não possui
        // Usamos whereDoesntHave para filtrar direto no banco de dados
        $availableBadges = Badge::whereDoesntHave('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        
        /** @var Badge $badge */
        foreach ($availableBadges as $badge) {
            if ($this->shouldAward($user, $badge)) {
                // syncWithoutDetaching garante que não haverá erro de duplicidade
                // e insere apenas se não existir a relação
                $user->badges()->syncWithoutDetaching([
                    $badge->id => ['earned_at' => now()]
                ]);
            }
        }
    }

    /**
     * Lógica de validação baseada no tipo da Badge
     */
    private function shouldAward(User $user, Badge $badge): bool
    {
        return match ($badge->type) {
            // Se for XP, compara com o xp_total do usuário
            'xp'      => $user->xp_total >= $badge->target_value,
            
            // Se for Quiz, conta quantos registros de progresso marcados como 'completed' ele tem
            'quizzes' => $user->quizzes()->wherePivot('completed', true)->count() >= $badge->target_value,
            
            // Se for Streak, olha a contagem de ofensiva atual
            'streak'  => $user->current_streak >= $badge->target_value,
            
            // Ranking geralmente é processado por um comando de fechamento de semana
            default   => false,
        };
    }
}