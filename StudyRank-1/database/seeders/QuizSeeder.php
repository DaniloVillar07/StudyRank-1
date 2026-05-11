<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Cria os 5 quizzes fixos da plataforma.
     *
     * Cada questão segue o formato exato esperado pelo QuizController:
     *   { "text": "...", "options": ["A","B","C","D"], "correct": 0 }
     *
     * "correct" é o índice (0-based) da opção correta.
     */
    public function run(): void
    {
        Quiz::truncate();

        $quizzes = [

            // ── 1. Fundamentos de JavaScript ──────────────────────────
            [
                'title'       => 'Fundamentos de JavaScript',
                'description' => 'Teste seus conhecimentos básicos em JavaScript.',
                'difficulty'  => 'easy',
                'xp_reward'   => 20,
                'is_active'   => true,
                'questions'   => [
                    [
                        'text'    => 'Qual palavra-chave declara uma variável imutável em JavaScript?',
                        'options' => ['var', 'let', 'const', 'static'],
                        'correct' => 2,
                    ],
                    [
                        'text'    => 'Qual operador verifica igualdade de valor E tipo?',
                        'options' => ['==', '===', '!=', '=>'],
                        'correct' => 1,
                    ],
                    [
                        'text'    => 'O que Array.map() retorna?',
                        'options' => [
                            'O mesmo array modificado',
                            'Um novo array com os resultados',
                            'O índice dos elementos',
                            'undefined',
                        ],
                        'correct' => 1,
                    ],
                ],
            ],

            // ── 2. React Essentials ────────────────────────────────────
            [
                'title'       => 'React Essentials',
                'description' => 'Conceitos fundamentais do React.',
                'difficulty'  => 'medium',
                'xp_reward'   => 20,
                'is_active'   => true,
                'questions'   => [
                    [
                        'text'    => 'Qual hook armazena estado em componentes funcionais?',
                        'options' => ['useEffect', 'useRef', 'useState', 'useContext'],
                        'correct' => 2,
                    ],
                    [
                        'text'    => 'O que é JSX?',
                        'options' => [
                            'Um banco de dados para React',
                            'Sintaxe que mistura HTML com JavaScript',
                            'Um framework CSS',
                            'Um gerenciador de pacotes',
                        ],
                        'correct' => 1,
                    ],
                    [
                        'text'    => 'Qual hook executa efeitos após a renderização?',
                        'options' => ['useState', 'useCallback', 'useEffect', 'useMemo'],
                        'correct' => 2,
                    ],
                ],
            ],

            // ── 3. CSS Avançado ────────────────────────────────────────
            [
                'title'       => 'CSS Avançado',
                'description' => 'Flexbox, Grid e animações CSS.',
                'difficulty'  => 'medium',
                'xp_reward'   => 20,
                'is_active'   => true,
                'questions'   => [
                    [
                        'text'    => 'Qual propriedade CSS3 cria layouts em grade?',
                        'options' => ['display: flex', 'display: grid', 'display: table', 'display: block'],
                        'correct' => 1,
                    ],
                    [
                        'text'    => 'No Flexbox, qual propriedade define a direção dos itens filhos?',
                        'options' => ['align-items', 'justify-content', 'flex-direction', 'flex-wrap'],
                        'correct' => 2,
                    ],
                    [
                        'text'    => 'Qual propriedade cria transições suaves entre estados?',
                        'options' => ['animation', 'transition', 'transform', '@keyframes'],
                        'correct' => 1,
                    ],
                ],
            ],

            // ── 4. Banco de Dados SQL ──────────────────────────────────
            [
                'title'       => 'Banco de Dados SQL',
                'description' => 'Queries e modelagem de dados.',
                'difficulty'  => 'hard',
                'xp_reward'   => 20,
                'is_active'   => true,
                'questions'   => [
                    [
                        'text'    => 'Qual comando SQL busca registros em uma tabela?',
                        'options' => ['GET', 'FETCH', 'SELECT', 'FIND'],
                        'correct' => 2,
                    ],
                    [
                        'text'    => 'Qual cláusula filtra registros APÓS um GROUP BY?',
                        'options' => ['WHERE', 'HAVING', 'FILTER', 'LIMIT'],
                        'correct' => 1,
                    ],
                    [
                        'text'    => 'O que é uma FOREIGN KEY?',
                        'options' => [
                            'Identifica unicamente cada linha',
                            'Um índice automático',
                            'Referencia a chave primária de outra tabela',
                            'Impede valores duplicados',
                        ],
                        'correct' => 2,
                    ],
                ],
            ],

            // ── 5. Git & Versionamento ─────────────────────────────────
            [
                'title'       => 'Git & Versionamento',
                'description' => 'Controle de versão e colaboração.',
                'difficulty'  => 'easy',
                'xp_reward'   => 20,
                'is_active'   => true,
                'questions'   => [
                    [
                        'text'    => 'Qual comando salva alterações no repositório local?',
                        'options' => ['git save', 'git push', 'git commit', 'git add'],
                        'correct' => 2,
                    ],
                    [
                        'text'    => 'O que "git pull" faz?',
                        'options' => [
                            'Envia commits para o remoto',
                            'Busca e integra alterações do remoto para o local',
                            'Cria uma nova branch',
                            'Reverte o último commit',
                        ],
                        'correct' => 1,
                    ],
                    [
                        'text'    => 'Qual comando cria e já muda para uma nova branch?',
                        'options' => [
                            'git branch nova',
                            'git checkout nova',
                            'git checkout -b nova',
                            'git merge nova',
                        ],
                        'correct' => 2,
                    ],
                ],
            ],
        ];

        foreach ($quizzes as $data) {
            Quiz::create($data);
        }

        $this->command->info('✓ 5 quizzes criados com sucesso.');
    }
}
