{{--
    Variáveis esperadas do RankingController@index:
    - $top10          → coleção de WeeklyXp com user carregado, ordenada por xp desc
    - $myWeeklyXp     → registro WeeklyXp do usuário logado nesta semana (ou null)
    - $maxXp          → XP do 1º colocado (para calcular as barras de progresso)
    - $quizzesDone    → quizzes que o usuário concluiu esta semana (para o desafio)
--}}

@extends('layouts.app')

@section('title', 'Ranking Semanal')

@section('styles')
<style>
    /* ── Banner da posição do usuário ── */
    .rank-banner {
        background: linear-gradient(90deg, #a020f0, #0d6efd);
        border-radius: 15px;
        padding: 22px 30px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    /* ── Container do ranking ── */
    .ranking-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    .ranking-card-header {
        background: linear-gradient(90deg, #a020f0, #0d6efd);
        color: white;
        padding: 14px 24px;
        font-weight: 700;
    }

    /* ── Linha do ranking ── */
    .ranking-row {
        display: flex;
        align-items: center;
        padding: 14px 24px;
        border-bottom: 1px solid #f5f5f5;
        transition: background 0.15s;
    }
    .ranking-row:last-child { border-bottom: none; }
    .ranking-row:hover { background: #fafafa; }

    /* Destaca a linha do usuário logado */
    .ranking-row.is-me {
        background: #fdf4ff;
        border-left: 3px solid #a020f0;
    }

    .rank-pos    { width: 36px; font-weight: 700; color: #555; }
    .rank-avatar {
        width: 44px; height: 44px;
        background: #f5f5f5;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        margin-right: 14px;
        font-size: 1.2rem;
    }
    .rank-name  { font-weight: 600; font-size: 0.9rem; }
    .rank-sub   { font-size: 0.75rem; color: #888; }
    .xp-bar     {
        width: 140px; height: 8px;
        background: #eee;
        border-radius: 10px;
        overflow: hidden;
        margin-left: auto;
    }
    .xp-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #a020f0, #0d6efd);
        border-radius: 10px;
    }

    /* ── Desafio da semana ── */
    .challenge-card {
        background: linear-gradient(135deg, #ff5f6d, #ffc371);
        border-radius: 20px;
        padding: 25px 30px;
        color: white;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }
    .challenge-icon { font-size: 2.5rem; flex-shrink: 0; }

    /* ── Cards de dicas ── */
    .tip-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        border: 1px solid #eee;
        height: 100%;
    }
    .tip-card .emoji { font-size: 1.5rem; margin-bottom: 8px; display: block; }
</style>
@endsection

@section('content')
<div class="container py-2 pb-5">

    {{-- Título --}}
    <div class="text-center mb-4">
        <h3 class="fw-bold">🏆 Ranking Semanal</h3>
        <p class="text-muted small">Competição reinicia toda segunda-feira</p>
    </div>

    {{-- ── Banner com a posição do usuário logado ── --}}
    <div class="rank-banner">
        <div class="d-flex align-items-center gap-3">
            <div style="font-size: 2rem;">👨‍💻</div>
            <div>
                <small style="opacity: 0.8;">Sua posição</small>
                <h5 class="mb-0 fw-bold">
                    {{-- Se o usuário não tem XP esta semana, não está ranqueado --}}
                    @if($myWeeklyXp && $myWeeklyXp->rank_position)
                        #{{ $myWeeklyXp->rank_position }}
                    @else
                        Não ranqueado
                    @endif
                </h5>
            </div>
        </div>
        <div class="text-end">
            <small style="opacity: 0.8;">XP desta semana</small>
            <h3 class="mb-0 fw-bold">{{ $myWeeklyXp?->xp_earned_this_week ?? 0 }}</h3>
        </div>
    </div>

    {{-- ── Top 10 ── --}}
    <div class="ranking-card">
        <div class="ranking-card-header">🥇 Top 10 da Semana</div>

        @forelse($top10 as $entry)
        @php
            $isMe = $entry->user_id === auth()->id();
            $pct  = $maxXp > 0 ? round(($entry->xp_earned_this_week / $maxXp) * 100) : 0;

            {{-- Ícone de medalha para top 3 -- }}
            $medal = match($loop->iteration) {
                1 => '🥇', 2 => '🥈', 3 => '🥉',
                default => $loop->iteration
            };
        @endphp
        <div class="ranking-row {{ $isMe ? 'is-me' : '' }}">
            <div class="rank-pos">{{ $medal }}</div>
            <div class="rank-avatar">👨‍💻</div>
            <div style="flex: 1;">
                <div class="rank-name">
                    {{ $entry->user->nickname }}
                    @if($isMe) <span style="color: #a020f0; font-size: 0.75rem;">(você)</span> @endif
                </div>
                <div class="rank-sub">{{ $entry->xp_earned_this_week }} XP esta semana</div>
            </div>
            <div class="xp-bar">
                <div class="xp-bar-fill" style="width: {{ $pct }}%"></div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">
            Nenhum usuário ranqueado ainda esta semana. Seja o primeiro! 🚀
        </div>
        @endforelse
    </div>

    {{-- ── Desafio da semana ── --}}
    <div class="challenge-card">
        <div class="challenge-icon">🔥</div>
        <div>
            <h6 class="fw-bold mb-1">Desafio da Semana</h6>
            <p class="small mb-2" style="opacity: 0.9;">
                Complete todos os 5 quizzes até domingo para ganhar um bônus de 50 XP!
            </p>
            {{-- Progresso visual do desafio semanal --}}
            <div class="d-flex align-items-center gap-2">
                <div style="background: rgba(255,255,255,0.3); border-radius: 10px; width: 160px; height: 8px;">
                    <div style="background: white; border-radius: 10px; height: 100%;
                                width: {{ min(($quizzesDone / 5) * 100, 100) }}%;">
                    </div>
                </div>
                <small style="opacity: 0.9;">{{ $quizzesDone }}/5 quizzes</small>
            </div>
        </div>
    </div>

    {{-- ── Dicas / Estratégia / Meta ── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="tip-card">
                <span class="emoji">📚</span>
                <h6 class="fw-bold">Dica</h6>
                <p class="small text-muted mb-0">Estude todo dia para manter seu streak</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card">
                <span class="emoji">⚡</span>
                <h6 class="fw-bold">Estratégia</h6>
                <p class="small text-muted mb-0">Complete quizzes difíceis para mais XP</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card">
                <span class="emoji">🎯</span>
                <h6 class="fw-bold">Meta</h6>
                <p class="small text-muted mb-0">Alcance o top 3 para ganhar badge especial</p>
            </div>
        </div>
    </div>

</div>
@endsection
