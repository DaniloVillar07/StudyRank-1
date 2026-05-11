<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * QuizFactory — usada exclusivamente nos testes automatizados.
 *
 * Cria quizzes com 3 questões simples e previsíveis:
 * todas com "correct": 0 para facilitar os testes de submissão.
 *
 * Uso:
 *   Quiz::factory()->create()
 *   Quiz::factory()->hard()->create()
 *   Quiz::factory()->inactive()->create()
 */
class QuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(3),
            'description' => fake()->sentence(8),
            'difficulty'  => 'easy',
            'xp_reward'   => 20,
            'is_active'   => true,
            // 3 questões com a resposta correta sempre no índice 0
            // Isso facilita montar $answers nos testes:
            //   $answers = array_fill(0, count($quiz->questions), 0)
            'questions'   => [
                [
                    'text'    => 'Questão de teste 1: qual é a opção A?',
                    'options' => ['Opção A (correta)', 'Opção B', 'Opção C', 'Opção D'],
                    'correct' => 0,
                ],
                [
                    'text'    => 'Questão de teste 2: qual é a opção A?',
                    'options' => ['Opção A (correta)', 'Opção B', 'Opção C', 'Opção D'],
                    'correct' => 0,
                ],
                [
                    'text'    => 'Questão de teste 3: qual é a opção A?',
                    'options' => ['Opção A (correta)', 'Opção B', 'Opção C', 'Opção D'],
                    'correct' => 0,
                ],
            ],
        ];
    }

    /** State: quiz difícil (hard). */
    public function hard(): static
    {
        return $this->state(fn () => [
            'difficulty' => 'hard',
            'xp_reward'  => 40,
        ]);
    }

    /** State: quiz desativado (não aparece na listagem). */
    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
