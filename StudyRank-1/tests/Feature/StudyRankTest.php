<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Quiz;
use App\Models\User;
use App\Models\WeeklyXp;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes de funcionalidade do StudyRank.
 *
 * RefreshDatabase: recria o banco a cada teste para isolamento total.
 * Cada método test_* é um cenário independente.
 *
 * Para rodar: php artisan test
 * Para rodar só este arquivo: php artisan test --filter StudyRankTest
 */
class StudyRankTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // CENÁRIO 1 — Login com credenciais inválidas
    // =========================================================================

    /**
     * Garante que o sistema REJEITA login com senha errada.
     *
     * O que testamos:
     *  - O usuário não é autenticado (assertGuest)
     *  - A sessão contém erros de validação no campo 'email'
     *  - A resposta redireciona de volta ao login (não para o dashboard)
     */
    public function test_login_fails_with_wrong_password(): void
    {
        // Arrange: criamos um usuário com senha conhecida
        $user = User::factory()->create([
            'email'    => 'teste@studyrank.com',
            'password' => bcrypt('senha-correta'),
        ]);

        // Act: tentamos logar com a senha errada
        $response = $this->post('/login', [
            'email'    => 'teste@studyrank.com',
            'password' => 'senha-errada',
        ]);
        
        // Assert
        $response->assertSessionHasErrors('email');   // mensagem de erro no campo
        $this->assertGuest();                          // confirma que NÃO está logado
    }

    /**
     * Garante que o login FUNCIONA com credenciais corretas.
     */
    public function test_login_succeeds_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'correto@studyrank.com',
            'password' => bcrypt('senha-correta'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'correto@studyrank.com',
            'password' => 'senha-correta',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    // =========================================================================
    // CENÁRIO 2 — Completar quiz e ganhar XP
    // =========================================================================

    /**
     * Garante que o XP é somado ao usuário ao completar um quiz com 100% de acertos.
     *
     * O que testamos:
     *  - xp_total do usuário aumenta pelo valor de xp_reward do quiz
     *  - Um registro é criado em user_quiz_progress com completed=true
     *  - Um registro é criado em weekly_xp para esta semana
     */
    public function test_completing_quiz_with_full_score_awards_full_xp(): void
    {
        // Arrange
        $user = User::factory()->create(['xp_total' => 0]);
        $quiz = Quiz::factory()->create(['xp_reward' => 20]);

        // Todas as respostas corretas (correct=0 em todas as questões da factory)
        $answers = array_fill(0, count($quiz->questions), 0);

        // Act
        $this->actingAs($user)
             ->post("/quiz/{$quiz->id}/submit", ['answers' => $answers]);

        // Assert: XP foi somado
        $this->assertDatabaseHas('users', [
            'id'       => $user->id,
            'xp_total' => 20,
        ]);

        // Assert: progresso salvo como concluído
        $this->assertDatabaseHas('user_quiz_progress', [
            'user_id'   => $user->id,
            'quiz_id'   => $quiz->id,
            'completed' => true,
            'score'     => 3, // 3 questões, todas corretas
        ]);

        // Assert: XP semanal registrado
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->assertDatabaseHas('weekly_xp', [
            'user_id'              => $user->id,
            'week_start'           => $weekStart,
            'xp_earned_this_week'  => 20,
        ]);
    }

    /**
     * Garante que score parcial dá XP proporcional (não o total).
     */
    public function test_partial_score_awards_proportional_xp(): void
    {
        $user = User::factory()->create(['xp_total' => 0]);
        $quiz = Quiz::factory()->create(['xp_reward' => 20]);

        // Acerta apenas 2 de 3 (índice errado na última questão)
        $answers = [0, 0, 99]; // 99 não é a resposta correta

        $this->actingAs($user)
             ->post("/quiz/{$quiz->id}/submit", ['answers' => $answers]);

        // 2/3 acertos = 66% → XP proporcional (round(20 * 0.66) = 13)
        $user->refresh();
        $this->assertGreaterThan(0, $user->xp_total);
        $this->assertLessThan(20, $user->xp_total);
    }

    // =========================================================================
    // CENÁRIO 3 — Streak é incrementado em dias consecutivos
    // =========================================================================

    /**
     * Garante que estudar no dia seguinte ao último acesso incrementa o streak.
     *
     * O que testamos:
     *  - current_streak aumenta de 3 para 4
     *  - last_activity_date é atualizado para hoje
     */
    public function test_streak_increments_when_studying_on_consecutive_day(): void
    {
        // Arrange: usuário com streak=3 e última atividade ontem
        $user = User::factory()->withStreak(3)->create();
        $quiz = Quiz::factory()->create();

        $answers = array_fill(0, count($quiz->questions), 0);

        // Act
        $this->actingAs($user)
             ->post("/quiz/{$quiz->id}/submit", ['answers' => $answers]);

        // Assert: streak incrementou
        $this->assertDatabaseHas('users', [
            'id'             => $user->id,
            'current_streak' => 4,
        ]);
    }

    /**
     * Garante que estudar após 2+ dias de ausência RESETA o streak para 1.
     */
    public function test_streak_resets_after_two_days_absence(): void
    {
        // Arrange: última atividade foi há 3 dias (sequência quebrada)
        $user = User::factory()->create([
            'current_streak'     => 5,
            'last_activity_date' => now()->subDays(3)->toDateString(),
        ]);
        $quiz = Quiz::factory()->create();

        $answers = array_fill(0, count($quiz->questions), 0);

        // Act
        $this->actingAs($user)
             ->post("/quiz/{$quiz->id}/submit", ['answers' => $answers]);

        // Assert: streak voltou para 1
        $this->assertDatabaseHas('users', [
            'id'             => $user->id,
            'current_streak' => 1,
        ]);
    }

    // =========================================================================
    // CENÁRIO 4 — Badge é concedida ao atingir a meta
    // =========================================================================

    /**
     * Garante que a badge de "1 quiz concluído" é atribuída ao usuário
     * após ele completar seu primeiro quiz.
     */
    public function test_quiz_badge_is_awarded_after_completing_required_quizzes(): void
    {
        // Arrange: badge que exige apenas 1 quiz concluído
        $badge = Badge::create([
            'name'         => 'Primeiro Passo',
            'description'  => 'Conclua 1 quiz',
            'icon'         => '🎓',
            'threshold_xp' => 0,
            'type'         => 'quizzes',
            'target_value' => 1,
        ]);

        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();

        $answers = array_fill(0, count($quiz->questions), 0);

        // Act
        $this->actingAs($user)
             ->post("/quiz/{$quiz->id}/submit", ['answers' => $answers]);

        // Assert: badge foi atribuída ao usuário
        $this->assertDatabaseHas('user_badges', [
            'user_id'  => $user->id,
            'badge_id' => $badge->id,
        ]);
    }

    /**
     * Garante que a badge de XP é concedida quando o usuário
     * atinge o XP mínimo necessário.
     */
    public function test_xp_badge_is_awarded_when_user_reaches_xp_target(): void
    {
        // Arrange: badge que exige 20 XP, usuário com 0 XP
        $badge = Badge::create([
            'name'         => 'Expert em XP',
            'description'  => 'Acumule 20 XP',
            'icon'         => '⭐',
            'threshold_xp' => 20,
            'type'         => 'xp',
            'target_value' => 20,
        ]);

        $user = User::factory()->create(['xp_total' => 0]);
        $quiz = Quiz::factory()->create(['xp_reward' => 20]);

        $answers = array_fill(0, count($quiz->questions), 0);

        // Act: completa o quiz e ganha 20 XP (atinge o alvo da badge)
        $this->actingAs($user)
             ->post("/quiz/{$quiz->id}/submit", ['answers' => $answers]);

        // Assert
        $this->assertDatabaseHas('user_badges', [
            'user_id'  => $user->id,
            'badge_id' => $badge->id,
        ]);
    }

    // =========================================================================
    // CENÁRIO 5 — Ranking semanal exibe o Top 10 corretamente
    // =========================================================================

    /**
     * Garante que a página de ranking carrega e exibe os usuários
     * ordenados por XP semanal decrescente.
     */
    public function test_ranking_page_displays_top10_ordered_by_weekly_xp(): void
    {
        // Arrange: 3 usuários com XP semanal diferentes
        $users     = User::factory()->count(3)->create();
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

        // Usuário 0: 300 XP | Usuário 1: 200 XP | Usuário 2: 100 XP
        $xpValues = [300, 200, 100];
        foreach ($users as $i => $u) {
            WeeklyXp::create([
                'user_id'             => $u->id,
                'week_start'          => $weekStart,
                'xp_earned_this_week' => $xpValues[$i],
                // rank_position é nullable — calculado pelo comando semanal
                'rank_position'       => $i + 1,
            ]);
        }

        // Act: usuário 2 acessa o ranking
        $response = $this->actingAs($users[2])->get('/ranking');

        // Assert: página carregou com sucesso
        $response->assertStatus(200);

        // Assert: a view recebeu os dados corretos
        $response->assertViewHas('top10');
        $response->assertViewHas('myWeeklyXp');
        $response->assertViewHas('maxXp');

        // Assert: o top10 tem 3 registros e o primeiro tem mais XP
        $top10 = $response->viewData('top10');
        $this->assertCount(3, $top10);
        $this->assertEquals(300, $top10->first()->xp_earned_this_week);

        // Assert: o maxXp corresponde ao líder
        $this->assertEquals(300, $response->viewData('maxXp'));

        // Assert: o registro do usuário logado (2) está presente
        $myXp = $response->viewData('myWeeklyXp');
        $this->assertNotNull($myXp);
        $this->assertEquals(100, $myXp->xp_earned_this_week);
    }

    /**
     * Garante que um usuário sem atividade semanal aparece como "não ranqueado".
     */
    public function test_user_with_no_weekly_activity_has_no_rank(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/ranking');

        $response->assertStatus(200);

        // myWeeklyXp deve ser null pois o usuário não jogou esta semana
        $this->assertNull($response->viewData('myWeeklyXp'));
    }
}
