{{--
    Variáveis esperadas do DashboardController@index:
    - $user         → auth()->user() com badges carregadas
    - $quizzes      → Quiz::where('is_active', true)->get() com user_progress
    - $allBadges    → Badge::all()
    - $completedCount → número de quizzes concluídos
    - $xp_needed    → XP necessário para o próximo nível (ex: 100)
--}}

@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
    /* ── Cards de estatística ── */
    .stat-card {
        border-radius: 15px;
        padding: 20px;
        color: white;
        height: 140px;
        position: relative;
        overflow: hidden;
        border: none;
    }
    .stat-card .icon-bg {
        position: absolute;
        right: 15px; top: 15px;
        font-size: 1.5rem;
        opacity: 0.8;
    }
    .stat-card .label  { font-size: 0.9rem; opacity: 0.9; }
    .stat-card .value  { font-size: 2rem; font-weight: bold; margin-top: 5px; }
    .stat-card .subtext { font-size: 0.8rem; opacity: 0.8; }
    .bg-xp      { background: #9d31ff; }
    .bg-streak  { background: #ff5e00; }
    .bg-quizzes { background: #007bff; }
    .bg-badges  { background: #00c853; }

    /* ── Barra de progresso de nível ── */
    .progress-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    /* ── Cards de quiz ── */
    .card-challenge {
        background: white;
        border-radius: 15px;
        border: 1px solid #eee;
        padding: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }
    .card-challenge:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    /* Quiz já completado recebe borda verde */
    .card-challenge.completed { border-color: #00c853; }
    .xp-tag { color: #a020f0; font-weight: bold; font-size: 0.9rem; }
    .badge-completed {
        background: #e8f5e9;
        color: #00c853;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
    }

    /* ── Cards de badge ── */
    .badge-card {
        background: white;
        border-radius: 15px;
        border: 1px solid #eee;
        padding: 20px;
        text-align: center;
        transition: all 0.3s;
    }
    .badge-card.locked { opacity: 0.4; filter: grayscale(100%); }
    .badge-icon { font-size: 2.5rem; margin-bottom: 10px; }
    .badge-card h6 { font-size: 0.95rem; margin-bottom: 5px; }
    .badge-card p  { font-size: 0.8rem; color: #888; margin-bottom: 0; }
</style>
@endsection

@section('content')
@php
    
    $xp_atual       = $user->xp_total % 100; 
    $progress_pct   = min(($xp_atual / $xp_needed) * 100, 100);
@endphp

<div class="container">

    {{-- ── Cards de estatísticas ── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-xp">
                <div class="icon-bg">⚡</div>
                <div class="label">XP Total</div>
                <div class="value">{{ $user->xp_total }}</div>
                <div class="subtext">
                    <span class="badge bg-white text-primary">Nível {{ $user->level }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-streak">
                <div class="icon-bg">🔥</div>
                <div class="label">Streak</div>
                <div class="value">{{ $user->current_streak }}</div>
                <div class="subtext">dias consecutivos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-quizzes">
                <div class="icon-bg">✅</div>
                <div class="label">Quizzes</div>
                {{-- Usamos $completedCount calculado no controller, não query na view --}}
                <div class="value">{{ $completedCount }}/5</div>
                <div class="subtext">{{ round(($completedCount / 5) * 100) }}% completo</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-badges">
                <div class="icon-bg">🏅</div>
                <div class="label">Badges</div>
                <div class="value">{{ $user->badges->count() }}</div>
                <div class="subtext">conquistadas</div>
            </div>
        </div>
    </div>

    {{-- ── Barra de progresso de nível ── --}}
    <div class="progress-container mb-5">
        <div class="d-flex justify-content-between mb-2">
            <span class="small fw-bold">Progresso para Nível {{ $user->level + 1 }}</span>
            <span class="small text-muted">{{ $xp_atual }}/{{ $xp_needed }} XP</span>
        </div>
        <div class="progress">
            <div class="progress-bar" style="width: {{ $progress_pct }}%"></div>
        </div>
    </div>

    {{-- ── Desafios disponíveis ── --}}
    <h4 class="mb-4 fw-bold">Desafios Disponíveis</h4>
    <div class="row g-4 mb-5">
        @foreach($quizzes as $quiz)
        @php
            {{-- user_progress foi injetado pelo QuizController@index -- }}
            $done = $quiz->user_progress?->completed === true;
        @endphp
        <div class="col-md-4">
            <div class="card-challenge {{ $done ? 'completed' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span style="font-size: 1.5rem;">📝</span>
                    <div class="d-flex align-items-center gap-2">
                        @if($done)
                            <span class="badge-completed">✓ Concluído</span>
                        @endif
                        <span class="xp-tag">+{{ $quiz->xp_reward }} XP</span>
                    </div>
                </div>
                <h6 class="fw-bold">{{ $quiz->title }}</h6>
                <p class="text-muted small">{{ Str::limit($quiz->description, 70) }}</p>

                {{--
                    route('quiz.show', $quiz->id) → gera /quiz/1, /quiz/2, etc.
                    Se já concluiu, o botão muda para "Refazer".
                --}}
                <a href="{{ route('quiz.show', $quiz->id) }}"
                   class="btn-purple w-100 text-center">
                    {{ $done ? 'Refazer' : 'Começar Agora' }}
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Badges / Conquistas ── --}}
    <h4 class="mb-4 fw-bold">Suas Conquistas</h4>
    <div class="row g-3 mb-5">
        @foreach($allBadges as $badge)
        @php
            {{-- $user->badges foi carregado via eager loading no controller -- }}
            $isOwned = $user->badges->contains($badge->id);
        @endphp
        <div class="col-md">
            <div class="badge-card {{ $isOwned ? '' : 'locked' }}">
                <div class="badge-icon">{{ $badge->icon }}</div>
                <h6>{{ $badge->name }}</h6>
                <p>{{ $badge->description }}</p>
                @if($isOwned)
                    <small class="text-success fw-bold">✓ Conquistada</small>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
