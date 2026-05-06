<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'threshold_xp',
        'type',
        'target_value',
    ];

    protected function casts(): array
    {
        return [
            'threshold_xp' => 'integer',
            'target_value' => 'integer',
        ];
    }

    /**
     * Relacionamento: Uma Badge pertence a muitos Usuários.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('id', 'earned_at') // Importante: incluímos o ID da pivot e o timestamp
                    ->using(UserBadge::class);    // Veremos isso no passo abaixo
    }
}