<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\User;
use App\Models\WeeklyXp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class XpService
{
    /**
     * Injetamos o BadgeService pelo construtor.
     * O Laravel resolve isso automaticamente (Dependency Injection).
     */
    public function __construct(private BadgeService $badgeService) {}

    // =========================================================================
    // MÉTODO PRINCIPAL — chame este após o usuário concluir um quiz
    // =========================================================================

    /**
     * Orquestra tudo que deve acontecer após um quiz ser completado:
     * XP, nível, streak, ranking semanal e badges.
     *
     * @param  User  $user   O usuário que completou o quiz
     * @param  Quiz  $quiz   O quiz que foi completado
     * @param  int   $score  Número de questões respondidas corretamente
     * @return array         Resultado com tudo que mudou (usado na view)
     */
    public function handleQuizCompletion(User $user, Quiz $quiz, int $score): array
    {
        // Usamos uma transaction para garantir consistência:
        // se qualquer etapa falhar, nenhuma alteração é salva no banco.
        return DB::transaction(function () use ($user, $quiz, $score) {

            // 1. Calcular o XP que o usuário ganhou neste quiz
            $xpEarned = $this->calculateXp($quiz, $score);

            // 2. Salvar o progresso do quiz na tabela user_quiz_progress
            $totalQuestions = count($quiz->questions);
            $this->saveQuizProgress($user, $quiz, $score, $xpEarned, $totalQuestions);

            // 3. Adicionar o XP ao total do usuário
            $user->xp_total += $xpEarned;

            // 4. Verificar se o usuário subiu de nível (retorna true/false)
            $leveledUp = $this->updateLevel($user);

            // 5. Atualizar o streak diário
            $streakUpdated = $this->updateStreak($user);

            // 6. Salvar as mudanças no usuário (xp_total, level, streak, last_activity_date)
            $user->save();

            // 7. Registrar o XP ganho no ranking semanal
            $this->registerWeeklyXp($user, $xpEarned);

            // 8. Verificar se alguma badge nova foi conquistada
            $badgesBefore = $user->badges->pluck('id');
            $this->badgeService->checkBadges($user->fresh()); // fresh() recarrega do banco
            $badgesAfter  = $user->fresh()->badges->pluck('id');

            // Descobrimos quais badges são novas comparando antes e depois
            $newBadgeIds = $badgesAfter->diff($badgesBefore);
            $newBadges   = $newBadgeIds->isNotEmpty()
                ? \App\Models\Badge::whereIn('id', $newBadgeIds)->get()
                : collect();

            // 9. Retornar um array com tudo que aconteceu — a view vai usar isso
            return [
                'xp_earned'       => $xpEarned,
                'score'           => $score,
                'total_questions' => $totalQuestions,
                'xp_total'        => $user->xp_total,
                'level'           => $user->level,
                'leveled_up'      => $leveledUp,
                'streak'          => $user->current_streak,
                'streak_updated'  => $streakUpdated,
                'new_badges'      => $newBadges,
            ];
        });
    }

    // =========================================================================
    // MÉTODOS PRIVADOS — cada um com uma responsabilidade única
    // =========================================================================

    /**
     * Calcula o XP ganho com base na proporção de acertos.
     *
     * Regra:
     *   - Acertou tudo (100%)        → XP completo do quiz
     *   - Acertou pelo menos 50%     → metade do XP (arredondado)
     *   - Menos de 50%               → 5 XP mínimo (não desanima o usuário)
     *
     * Exemplos com quiz de 20 XP e 3 questões:
     *   score=3 → 20 XP | score=2 → 13 XP | score=1 → 5 XP
     */
    private function calculateXp(Quiz $quiz, int $score): int
    {
        $total = count($quiz->questions);

        if ($total === 0) {
            return 0;
        }

        $percentage = $score / $total;

        return match (true) {
            $percentage >= 1.0 => $quiz->xp_reward,
            $percentage >= 0.5 => (int) round($quiz->xp_reward * $percentage),
            default            => 5,
        };
    }

    /**
     * Salva ou atualiza o progresso do usuário neste quiz.
     *
     * updateOrCreate lida com retentativas: se o usuário fez o quiz antes,
     * o registro é atualizado com os novos dados (não duplica).
     */
    private function saveQuizProgress(
        User $user,
        Quiz $quiz,
        int  $score,
        int  $xpEarned,
        int  $totalQuestions
    ): void {
        $user->quizProgress()->updateOrCreate(
            ['user_id' => $user->id, 'quiz_id' => $quiz->id],
            [
                'completed'   => true,
                'score'       => $score,
                'xp_earned'   => $xpEarned,
                'finished_at' => now(),
            ]
        );
    }

    /**
     * Recalcula o nível com base no XP total.
     *
     * Fórmula: a cada 100 XP acumulados, sobe 1 nível. Mínimo = 1.
     *
     *   0 XP  → Nível 1 | 100 XP → Nível 2 | 250 XP → Nível 3
     *
     * @return bool true se houve aumento de nível
     */
    private function updateLevel(User $user): bool
    {
        $newLevel = (int) floor($user->xp_total / 100) + 1;

        if ($newLevel > $user->level) {
            $user->level = $newLevel;
            return true;
        }

        return false;
    }

    /**
     * Atualiza o streak (sequência de dias consecutivos de estudo).
     *
     * Cenário A — estudou ontem    → incrementa (sequência continua)
     * Cenário B — já estudou hoje  → não faz nada (já foi contado)
     * Cenário C — 2+ dias de pausa → reset para 1 (sequência quebrada)
     *
     * @return bool true se o streak foi incrementado (cenário A)
     */
    private function updateStreak(User $user): bool
    {
        $today    = Carbon::today();
        $lastDate = $user->last_activity_date
            ? Carbon::parse($user->last_activity_date)
            : null;

        // Cenário B: já estudou hoje
        if ($lastDate && $lastDate->isToday()) {
            return false;
        }

        // Cenário A: estudou ontem, sequência continua
        if ($lastDate && $lastDate->isYesterday()) {
            $user->current_streak    += 1;
            $user->last_activity_date = $today;
            return true;
        }

        // Cenário C: sequência quebrada, reinicia
        $user->current_streak     = 1;
        $user->last_activity_date = $today;
        return false;
    }

    /**
     * Registra o XP ganho no ranking semanal.
     *
     * A semana sempre começa na segunda-feira.
     * Se já existe um registro para esta semana, somamos o XP.
     * Se não existe, criamos com o valor inicial.
     */
    private function registerWeeklyXp(User $user, int $xpEarned): void
    {
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

        $record = WeeklyXp::firstOrCreate(
            ['user_id' => $user->id, 'week_start' => $weekStart],
            ['xp_earned_this_week' => 0]
        );

        $record->increment('xp_earned_this_week', $xpEarned);
    }
}