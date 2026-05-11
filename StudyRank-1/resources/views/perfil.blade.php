{{--
    Variáveis esperadas do ProfileController@index:
    - $user         → auth()->user() com badges e quizProgress carregados
    - $allBadges    → Badge::all()
    - $xp_needed    → XP para o próximo nível
    - $rankPosition → posição do usuário no ranking semanal (ou 'Não ranqueado')
--}}

@extends('layouts.app')

@section('title', 'Perfil')

@section('styles')
<style>
    /* ── Cabeçalho do perfil ── */
    .profile-header {
        background: linear-gradient(135deg, #a020f0, #6f42c1);
        border-radius: 20px;
        padding: 35px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
    }
    .avatar-large {
        width: 90px; height: 90px;
        background: #fdf2e9;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        border: 4px solid rgba(255,255,255,0.5);
    }

    /* ── Cards de estatística do perfil ── */
    .stat-box {
        background: white;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        border: 1px solid #eee;
        height: 100%;
        transition: transform 0.2s;
    }
    .stat-box:hover { transform: translateY(-4px); }
    .stat-val {
        font-size: 1.6rem;
        font-weight: 700;
        color: #a020f0;
        display: block;
        margin-bottom: 4px;
    }
    .stat-label {
        font-size: 0.78rem;
        color: #888;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ── Barra de progresso ── */
    .progress-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        border: 1px solid #eee;
        margin-bottom: 30px;
    }

    /* ── Grid de badges ── */
    .badge-item {
        background: white;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        border: 1px solid #eee;
        transition: all 0.3s;
        height: 100%;
    }
    .badge-item.locked { opacity: 0.45; filter: grayscale(1); }
    .badge-item:not(.locked) { border-color: #a020f0; }
    .badge-item-icon { font-size: 2.2rem; margin-bottom: 8px; display: block; }
    .badge-item-name { font-weight: 700; font-size: 0.9rem; margin-bottom: 4px; }
    .badge-item-desc { font-size: 0.75rem; color: #999; line-height: 1.3; }

    /* ── Histórico de quizzes ── */
    .quiz-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    .quiz-row:last-child { border-bottom: none; }
    .score-pill {
        background: #f3e8ff;
        color: #a020f0;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
    }
</style>
@endsection

@section('content')
@php
    $xp_atual     = $user->xp_total % 100;
    $progress_pct = min(($xp_atual / $xp_needed) * 100, 100);
    $quizzesDone  = $user->quizProgress->where('completed', true)->count();
@endphp

<div class="container pb-5">
    <div class="row g-4">

        {{-- ── Coluna esquerda: Info do usuário ── --}}
        <div class="col-lg-4">

            {{-- Cabeçalho roxo com avatar --}}
            <div class="profile-header">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="avatar-large">👨‍💻</div>
                    <div>
                        <div class="fw-bold fs-5">{{ $user->nickname }}</div>
                        <div style="opacity: 0.8; font-size: 0.85rem;">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-white text-purple fw-bold" style="color: #a020f0;">
                        Nível {{ $user->level }}
                    </span>
                    <span class="badge bg-white text-muted fw-bold">
                        {{ $user->xp_total }} XP
                    </span>
                </div>
            </div>

            {{-- Barra de progresso de nível --}}
            <div class="progress-card">
                <div class="d-flex justify-content-between mb-2">
                    <span class="small fw-bold">Progresso → Nível {{ $user->level + 1 }}</span>
                    <span class="small text-muted">{{ $xp_atual }}/{{ $xp_needed }} XP</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" style="width: {{ $progress_pct }}%"></div>
                </div>
            </div>

            {{-- Estatísticas rápidas --}}
            <div class="row g-3">
                <div class="col-6">
                    <div class="stat-box">
                        <span class="stat-val">{{ $user->current_streak }} 🔥</span>
                        <span class="stat-label">Streak</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box">
                        <span class="stat-val">{{ $quizzesDone }} ✅</span>
                        <span class="stat-label">Quizzes</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box">
                        <span class="stat-val">{{ $user->badges->count() }} 🏅</span>
                        <span class="stat-label">Badges</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box">
                        {{-- $rankPosition vem do ProfileController --}}
                        <span class="stat-val" style="font-size: 1.1rem;">
                            {{ is_numeric($rankPosition) ? '#'.$rankPosition : $rankPosition }}
                        </span>
                        <span class="stat-label">Ranking</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Coluna direita: Badges + Histórico ── --}}
        <div class="col-lg-8">

            {{-- Coleção de Badges --}}
            <h5 class="fw-bold mb-3">Coleção de Badges</h5>
            <div class="row g-3 mb-5">
                @foreach($allBadges as $badge)
                @php $isOwned = $user->badges->contains($badge->id); @endphp
                <div class="col-md-4">
                    <div class="badge-item {{ $isOwned ? '' : 'locked' }}">
                        <span class="badge-item-icon">{{ $badge->icon }}</span>
                        <div class="badge-item-name">{{ $badge->name }}</div>
                        <div class="badge-item-desc">{{ $badge->description }}</div>
                        @if($isOwned)
                            <div class="mt-2">
                                <small class="text-success fw-bold">✓ Conquistada</small>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Histórico de quizzes realizados --}}
            <h5 class="fw-bold mb-3">Atividade Recente</h5>
            <div class="bg-white rounded-4 p-4 border" style="border-color: #eee !important;">
                @forelse($user->quizProgress->where('completed', true) as $progress)
                <div class="quiz-row">
                    <div>
                        <div class="fw-semibold small">{{ $progress->quiz->title }}</div>
                        <div class="text-muted" style="font-size: 0.78rem;">
                            {{-- finished_at é um Carbon graças ao cast no model --}}
                            {{ $progress->finished_at?->format('d/m/Y \à\s H:i') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="score-pill">
                            {{ $progress->score }}/{{ count($progress->quiz->questions) }} acertos
                        </span>
                        <span class="fw-bold text-success small">+{{ $progress->xp_earned }} XP</span>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center mb-0 py-3">
                    Nenhum quiz concluído ainda. <a href="{{ route('dashboard') }}">Comece agora!</a>
                </p>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection
