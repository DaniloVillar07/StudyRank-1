<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Campos que podem ser preenchidos em massa (Mass Assignment)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nickname',
        'avatar',
        'xp_total',
        'level',
        'current_streak',
        'last_activity_date',
    ];

    /**
     * Campos que devem ficar ocultos (ex: ao retornar um JSON)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversão automática de tipos (Casting)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_activity_date' => 'date', // Garante que o Laravel trate isso como um objeto de data
        ];
    }

    /* ---------------------------------------------------------
     * RELACIONAMENTOS (A Mágica do Eloquent)
     * --------------------------------------------------------- */

    /**
     * Um usuário pode ter progresso em vários quizzes.
     */
    public function quizProgress()
    {
        return $this->hasMany(UserQuizProgress::class);
    }

    /**
     * Um usuário tem muitos Quizzes (através da tabela pivot user_quiz_progress).
     */
    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'user_quiz_progress')
                    ->withPivot('completed', 'score', 'xp_earned', 'finished_at') // Traz os dados extras da tabela intermediária
                    ->withTimestamps();
    }

    /**
     * Retorna APENAS os quizzes que o usuário já concluiu.
     * Reutiliza a relação quizzes() mas aplica um filtro na tabela pivot.
     */
    public function completedQuizzes()
    {
        return $this->quizzes()->wherePivot('completed', true);
    }

    /**
     * Um usuário tem muitos Badges (Conquistas).
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at'); // Traz a data em que ganhou a conquista
    }

    /**
     * Um usuário tem vários registros de XP Semanal (Histórico do Ranking).
     */
    public function weeklyXps()
    {
        return $this->hasMany(WeeklyXp::class);
    }

    /* ---------------------------------------------------------
     * MÉTODOS AUXILIARES
     * --------------------------------------------------------- */

    /**
     * Atualiza a ofensiva (streak) do usuário.
     */
    public function updateStreak()
    {
        // Incrementa a coluna current_streak (definida no seu fillable)
        $this->increment('current_streak');
        $this->update(['last_activity_date' => now()]);
    }
}