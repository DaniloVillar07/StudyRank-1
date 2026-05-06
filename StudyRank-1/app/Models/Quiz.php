<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos (Mass Assignment).
     */
    protected $fillable = [
        'title',
        'description',
        'difficulty',
        'xp_reward',
        'questions',
        'is_active',
    ];

    /**
     * Conversão de tipos (Casting).
     * O campo 'questions' é o mais importante aqui.
     */
    protected function casts(): array
    {
        return [
            'questions' => 'array',   // Converte o JSON do SQLite automaticamente para Array PHP
            'is_active' => 'boolean', // Garante que 0/1 do banco vire false/true
            'xp_reward' => 'integer',
        ];
    }

    /* ---------------------------------------------------------
     * RELACIONAMENTOS
     * --------------------------------------------------------- */

    /**
     * Um Quiz pode ser realizado por muitos usuários.
     * Relacionamento N:N (Muitos para Muitos)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_quiz_progress')
                    ->withPivot('completed', 'score', 'xp_earned', 'finished_at')
                    ->withTimestamps();
    }
}