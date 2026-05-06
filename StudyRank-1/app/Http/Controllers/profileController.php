<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $badges = $user->badges()->get();
        $completedQuizzes = $user->completedQuizzes()->count();

        return view('profile.index', compact('user', 'badges', 'completedQuizzes'));
    }
}