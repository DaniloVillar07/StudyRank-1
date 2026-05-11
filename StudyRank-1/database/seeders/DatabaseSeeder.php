<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ordem importa:
     *  1. BadgeSeeder  — cria as 5 badges (sem dependências)
     *  2. QuizSeeder   — cria os 5 quizzes (sem dependências)
     *  3. UserSeeder   — cria usuários de teste (depende de badges/quizzes para associar)
     *
     * Para rodar: php artisan db:seed
     * Para resetar e rodar: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            BadgeSeeder::class,
            QuizSeeder::class,
            UserSeeder::class,
        ]);
    }
}
