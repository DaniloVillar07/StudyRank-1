<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Services\XpService;
use Illuminate\Http\Request;

/**
 * QuizController
 *
 * Três responsabilidades:
 *   index()  → lista quizzes disponíveis
 *   show()   → exibe as perguntas de um quiz
 *   submit() → corrige respostas e aciona o XpService
 */
class QuizController extends Controller
{
    // O Laravel injeta o XpService automaticamente (Dependency Injection)
    public function __construct(private XpService $xpService) {}

    // =========================================================================
    // INDEX
    // =========================================================================

    /**
     * Lista todos os quizzes ativos com o progresso do usuário.
     */
    public function index()
    {
        $user = auth()->user();

        $quizzes = Quiz::where('is_active', true)
            ->with(['users' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get()
            ->map(function (Quiz $quiz) {
                $quiz->user_progress = $quiz->users->first()?->pivot;
                return $quiz;
            });

        $completedCount = $quizzes->filter(
            fn($q) => $q->user_progress?->completed === true
        )->count();

        return view('quiz.index', compact('quizzes', 'completedCount'));
    }

    // =========================================================================
    // SHOW
    // =========================================================================

    /**
     * Exibe a tela de perguntas de um quiz.
     * Route Model Binding: /quiz/3 → $quiz = Quiz::findOrFail(3)
     */
    public function show(Quiz $quiz)
    {
        if (! $quiz->is_active) {
            return redirect()->route('dashboard')
                ->with('error', 'Este quiz não está disponível no momento.');
        }

        $progress = auth()->user()->quizProgress()
            ->where('quiz_id', $quiz->id)
            ->first();

        if ($progress?->completed) {
            return redirect()->route('dashboard')
                ->with('info', "Você já completou \"{$quiz->title}\". Refaça para praticar!");
        }

        return view('quiz.show', compact('quiz'));
    }

    // =========================================================================
    // SUBMIT
    // =========================================================================

    /**
     * Recebe as respostas, corrige e aciona o XpService.
     *
     * Estrutura esperada do POST:
     *   answers[0] = 1   ← índice da opção escolhida para a questão 0
     *   answers[1] = 0
     *
     * Estrutura do JSON de cada questão no banco:
     *   { "text": "...", "options": [...], "correct": 1 }
     *                                               ↑ índice correto (0-based)
     *
     * CORREÇÃO em relação ao controller anterior:
     *   - Usa 'correct' (não 'answer') como chave do JSON
     *   - Usa XpService em vez de lógica inline
     *   - Retorna view resultado (não redirect para dashboard)
     */
    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'answers'   => ['required', 'array'],
            'answers.*' => ['nullable', 'integer'],
        ]);

        $score = $this->corrigir($quiz->questions, $request->input('answers', []));

        // XpService cuida de tudo: XP, nível, streak, ranking semanal e badges
        $resultado = $this->xpService->handleQuizCompletion(
            auth()->user(),
            $quiz,
            $score
        );

        return view('quiz.resultado', compact('resultado', 'quiz'));
    }

    // =========================================================================
    // PRIVADO
    // =========================================================================

    /**
     * Compara respostas do usuário com a chave 'correct' do JSON.
     * Usa == (não ===) pois o valor pode vir como string do form HTML.
     */
    private function corrigir(array $questions, array $answers): int
    {
        $score = 0;

        foreach ($questions as $index => $question) {
            $userAnswer = $answers[$index] ?? null;

            if ($userAnswer !== null && $userAnswer == $question['correct']) {
                $score++;
            }
        }

        return $score;
    }
}
