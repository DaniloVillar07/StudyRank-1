<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $quizzes = [
            [
                'title' => 'PHP Moderno',
                'description' => 'Domine tipos, arrow functions e novidades do PHP 8.x.',
                'difficulty' => 'easy',
                'xp_reward' => 50,
                'questions' => [
                    [
                        'q' => 'Qual símbolo é usado para o Nullsafe Operator no PHP?',
                        'options' => ['??', '?:', '?->', '!!'],
                        'answer' => 2
                    ],
                    [
                        'q' => 'Arrow functions no PHP têm acesso automático ao escopo pai?',
                        'options' => ['Sim', 'Não', 'Apenas se usar global', 'Apenas variáveis estáticas'],
                        'answer' => 0
                    ]
                ],
            ],
            [
                'title' => 'Laravel: Rotas e Controllers',
                'description' => 'Conceitos essenciais de fluxo de requisição.',
                'difficulty' => 'easy',
                'xp_reward' => 50,
                'questions' => [
                    [
                        'q' => 'Qual comando Artisan cria um Controller do tipo Resource?',
                        'options' => ['make:controller --r', 'make:controller --resource', 'generate:resource', 'create:controller'],
                        'answer' => 1
                    ]
                ],
            ],
            [
                'title' => 'Eloquent & Banco de Dados',
                'description' => 'Consultas avançadas e relacionamentos.',
                'difficulty' => 'medium',
                'xp_reward' => 100,
                'questions' => [
                    [
                        'q' => 'Como evitar o problema de consulta N+1 no Eloquent?',
                        'options' => ['Usando with()', 'Usando find()', 'Usando all()', 'Usando join()'],
                        'answer' => 0
                    ]
                ],
            ],
            [
                'title' => 'Git para Profissionais',
                'description' => 'Gestão de branches e resolução de conflitos.',
                'difficulty' => 'medium',
                'xp_reward' => 80,
                'questions' => [
                    [
                        'q' => 'Qual comando remove um arquivo do index do Git sem apagá-lo do disco?',
                        'options' => ['git rm', 'git delete --cached', 'git rm --cached', 'git reset hard'],
                        'answer' => 2
                    ]
                ],
            ],
            [
                'title' => 'Lógica de RPG & Sistemas',
                'description' => 'Desafios de lógica baseados em sistemas de torneios.',
                'difficulty' => 'hard',
                'xp_reward' => 150,
                'questions' => [
                    [
                        'q' => 'Em um torneio de "Aventureiros em ReinFord", qual a melhor forma de garantir integridade referencial entre times e grupos?',
                        'options' => ['Usar chaves estrangeiras (FK)', 'Validar apenas no PHP', 'Usar arquivos TXT', 'Não usar chaves'],
                        'answer' => 0
                    ]
                ],
            ],
        ];

        foreach ($quizzes as $quiz) {
            Quiz::create($quiz);
        }
    }
}