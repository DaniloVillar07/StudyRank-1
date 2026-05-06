<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // Medalhas por quantidade de Quizzes Concluídos
            [
                'name'         => 'Primeiros Passos',
                'description'  => 'Concluiu seu primeiro quiz no StudyRank.',
                'icon'         => 'fa-graduation-cap',
                'type'         => 'quizzes',
                'target_value' => 1,
            ],
            [
                'name'         => 'Maratonista de Estudos',
                'description'  => 'Completou 10 quizzes com sucesso.',
                'icon'         => 'fa-running',
                'type'         => 'quizzes',
                'target_value' => 10,
            ],

            // Medalhas por XP Acumulado
            [
                'name'         => 'Acumulador de Conhecimento',
                'description'  => 'Alcançou a marca de 500 XP total.',
                'icon'         => 'fa-brain',
                'type'         => 'xp',
                'threshold_xp' => 500,
                'target_value' => 500,
            ],

            // Medalhas por Ofensiva (Streak)
            [
                'name'         => 'Foco Total',
                'description'  => 'Manteve uma sequência de 7 dias ativos.',
                'icon'         => 'fa-fire',
                'type'         => 'streak',
                'target_value' => 7,
            ],

            // Medalhas por Ranking
            [
                'name'         => 'Lenda da Semana',
                'description'  => 'Ficou em 1º lugar no ranking semanal.',
                'icon'         => 'fa-crown',
                'type'         => 'ranking',
                'target_value' => 1, // Representa a 1ª posição
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
