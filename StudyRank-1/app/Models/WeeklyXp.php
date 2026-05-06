<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyXp extends Model
{
    use HasFactory;

    /**
     * Como essa tabela é para histórico e ranking, 
     * geralmente não precisamos de updated_at, mas vamos manter o padrão.
     */
    protected $table = 'weekly_xp';

    /**
     * Desativamos timestamps se a sua migration não os criou. 
     * Caso tenha criado, mude para true.
     */
    public $timestamps = false;

    protected $fillable = [
        'week_start',
        'user_id',
        'xp_earned_this_week',
        'rank_position',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'xp_earned_this_week' => 'integer',
            'rank_position' => 'integer',
        ];
    }

    /* ---------------------------------------------------------
     * RELACIONAMENTOS
     * --------------------------------------------------------- */

    /**
     * Scope para buscar registros de uma semana específica de forma limpa.
     */
    public function scopeDaSemana($query, $data)
    {
        return $query->whereDate('week_start', $data);
    }
    /**
     * O registro de XP semanal pertence a um Usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}