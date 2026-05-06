<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    //
}
class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Busca os badges do usuário
        $badges = $user->badges()->orderBy('user_badges.earned_at', 'desc')->get();

        // Quizzes concluídos
        $completedQuizzes = $user->completedQuizzes()->count();
        
        // Total de quizzes disponíveis
        $totalQuizzes = \App\Models\Quiz::count();

        return view('dashboard', compact('user', 'badges', 'completedQuizzes', 'totalQuizzes'));
    }
}