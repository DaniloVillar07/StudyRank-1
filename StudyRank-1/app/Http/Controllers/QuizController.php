<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\UserQuizProgress;
use App\Services\BadgeService; // Importado para usar a lógica de medalhas
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::all();
        $user = auth()->user();

        // Pega o progresso do usuário em todos os quizzes
        $progress = UserQuizProgress::where('user_id', $user->id)
                    ->pluck('completed', 'quiz_id');

        return view('quizzes.index', compact('quizzes', 'progress'));
    }

    public function show(Quiz $quiz)
    {
        $user = auth()->user();
        
        // Verifica se o usuário já completou este quiz
        $progress = UserQuizProgress::where('user_id', $user->id)
                    ->where('quiz_id', $quiz->id)
                    ->first();

        return view('quizzes.show', compact('quiz', 'progress'));
    }

    // Adicionamos o BadgeService como dependência aqui
    public function submit(Request $request, Quiz $quiz, BadgeService $badgeService)
    {
        $user = auth()->user();
        $answers = $request->input('answers', []);

        $questions = $quiz->questions;
        $score = 0;
        $totalQuestions = count($questions);

        // Corrige as respostas
        foreach ($questions as $index => $question) {
            $userAnswer = $answers[$index] ?? null;
            if ($userAnswer !== null && (int)$userAnswer === (int)$question['answer']) {
                $score++;
            }
        }

        $percentage = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;

        // Atualiza ou cria o progresso
        $progress = UserQuizProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'quiz_id' => $quiz->id
            ],
            [
                'completed' => true,
                'score' => $score,
                'finished_at' => now()
            ]
        );

        $xpGained = 0;
        $message = '';

        // Só dá XP se acertar pelo menos 60%
        if ($percentage >= 60) {
            // AJUSTE: 'points' alterado para 'xp_reward' conforme sua migration
            $xpGained = $quiz->xp_reward; 
            
            // Bônus de streak
            $user->updateStreak();
            // AJUSTE: 'streak' alterado para 'current_streak' conforme seu Model User
            $streakBonus = $user->current_streak * 5; 
            $xpGained += $streakBonus;

            // AJUSTE: 'xp' alterado para 'xp_total' conforme seu Model User
            $user->increment('xp_total', $xpGained);

            // Atualiza nível (a cada 100 XP sobe 1 nível)
            $newLevel = intdiv($user->xp_total, 100) + 1;
            if ($newLevel > $user->level) {
                $user->update(['level' => $newLevel]);
            }

            // Chamada do Service que criamos anteriormente
            $badgeService->checkBadges($user);

            $message = "Parabéns! +{$xpGained} XP (incluindo bônus de streak)";
        } else {
            $message = "Você acertou {$score} de {$totalQuestions} perguntas. Tente novamente para ganhar XP!";
        }

        return redirect()->route('dashboard')
                         ->with('success', $message)
                         ->with('xp_gained', $xpGained)
                         ->with('percentage', round($percentage));
    }
}