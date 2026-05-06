<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserQuizProgress extends Pivot
{
    /**
     * Como sua migration tem um ID próprio, avisamos ao Laravel
     */
    public $incrementing = true;

    protected $table = 'user_quiz_progress';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'completed',
        'score',
        'xp_earned',
        'finished_at',
    ];

    /**
     * Conversão de tipos
     */
    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'score' => 'integer',
            'xp_earned' => 'integer',
            'finished_at' => 'datetime',
        ];
    }

    /* ---------------------------------------------------------
     * RELACIONAMENTOS (OPCIONAIS MAS ÚTEIS)
     * --------------------------------------------------------- */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}