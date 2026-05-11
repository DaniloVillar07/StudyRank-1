{{--
    Variáveis esperadas (retornadas pelo XpService via QuizController@submit):
    $resultado = [
        'xp_earned'       => int,
        'score'           => int,
        'total_questions' => int,
        'xp_total'        => int,
        'level'           => int,
        'leveled_up'      => bool,
        'streak'          => int,
        'streak_updated'  => bool,
        'new_badges'      => Collection<Badge>,
    ]
    $quiz → instância de Quiz
--}}

@extends('layouts.app')

@section('title', 'Resultado')

@section('styles')
<style>
    /* ── Card principal do resultado ── */
    .result-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        max-width: 560px;
        margin: 0 auto;
    }

    /* ── Score (círculo grande) ── */
    .score-circle {
        width: 110px; height: 110px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        font-weight: 700;
    }
    .score-circle.great  { background: #e8f5e9; color: #2e7d32; border: 3px solid #66bb6a; }
    .score-circle.good   { background: #fff3e0; color: #e65100; border: 3px solid #ffa726; }
    .score-circle.try    { background: #fce4ec; color: #c62828; border: 3px solid #ef9a9a; }
    .score-circle-num    { font-size: 1.8rem; line-height: 1; }
    .score-circle-label  { font-size: 0.7rem; opacity: 0.8; text-transform: uppercase; }

    /* ── Linha de stat (XP, streak, etc.) ── */
    .stat-row {
        display: flex;
        justify-content: space-around;
        background: #fafafa;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 24px;
    }
    .stat-item { text-align: center; }
    .stat-item .val {
        font-size: 1.4rem;
        font-weight: 700;
        color: #a020f0;
        display: block;
    }
    .stat-item .lbl { font-size: 0.75rem; color: #888; text-transform: uppercase; }

    /* ── Badge nova ── */
    .badge-new {
        background: linear-gradient(135deg, #a020f0, #6f42c1);
        border-radius: 15px;
        padding: 16px 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 12px;
        text-align: left;
    }
    .badge-new-icon { font-size: 2rem; flex-shrink: 0; }

    /* ── Level up ── */
    .levelup-banner {
        background: linear-gradient(135deg, #ff9800, #ffc107);
        border-radius: 14px;
        padding: 16px 20px;
        color: white;
        font-weight: 700;
        margin-bottom: 24px;
        font-size: 1.05rem;
        animation: pulse 1s ease-in-out 3;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50%       { transform: scale(1.02); }
    }

    /* ── Streak atualizado ── */
    .streak-banner {
        background: #fff3e0;
        border: 1px solid #ffcc80;
        border-radius: 12px;
        padding: 12px 18px;
        color: #e65100;
        font-weight: 600;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
<div class="result-card">

    {{-- ── Título ── --}}
    <h4 class="fw-bold mb-1">{{ $quiz->title }}</h4>
    <p class="text-muted small mb-4">Quiz concluído!</p>

    {{-- ── Círculo de score ── --}}
    @php
        $pct   = $resultado['total_questions'] > 0
            ? ($resultado['score'] / $resultado['total_questions']) * 100
            : 0;
        $class = $pct >= 80 ? 'great' : ($pct >= 50 ? 'good' : 'try');
        $emoji = $pct >= 80 ? '🎉'   : ($pct >= 50 ? '👍'   : '💪');
    @endphp
    <div class="score-circle {{ $class }}">
        <span class="score-circle-num">{{ $resultado['score'] }}/{{ $resultado['total_questions'] }}</span>
        <span class="score-circle-label">acertos</span>
    </div>

    {{-- Mensagem de encorajamento --}}
    <p class="fw-semibold mb-4" style="font-size: 1.05rem;">
        @if($pct >= 80) {{ $emoji }} Excelente! Continue assim!
        @elseif($pct >= 50) {{ $emoji }} Bom trabalho! Você está evoluindo.
        @else {{ $emoji }} Não desanime! Pratique e melhore!
        @endif
    </p>

    {{-- ── Level up ── --}}
    @if($resultado['leveled_up'])
    <div class="levelup-banner">
        🎊 Parabéns! Você subiu para o Nível {{ $resultado['level'] }}!
    </div>
    @endif

    {{-- ── Streak atualizado ── --}}
    @if($resultado['streak_updated'])
    <div class="streak-banner">
        🔥 Sequência mantida! Você está em {{ $resultado['streak'] }} dias consecutivos!
    </div>
    @endif

    {{-- ── Linha de estatísticas ── --}}
    <div class="stat-row">
        <div class="stat-item">
            <span class="val">+{{ $resultado['xp_earned'] }}</span>
            <span class="lbl">XP Ganho</span>
        </div>
        <div class="stat-item">
            <span class="val">{{ $resultado['xp_total'] }}</span>
            <span class="lbl">XP Total</span>
        </div>
        <div class="stat-item">
            <span class="val">{{ $resultado['streak'] }} 🔥</span>
            <span class="lbl">Streak</span>
        </div>
        <div class="stat-item">
            <span class="val">Nível {{ $resultado['level'] }}</span>
            <span class="lbl">Nível atual</span>
        </div>
    </div>

    {{-- ── Badges novas conquistadas ── --}}
    @if($resultado['new_badges']->isNotEmpty())
    <div class="text-start mb-4">
        <h6 class="fw-bold mb-3">🏅 Badges conquistadas!</h6>
        @foreach($resultado['new_badges'] as $badge)
        <div class="badge-new">
            <div class="badge-new-icon">{{ $badge->icon }}</div>
            <div>
                <div class="fw-bold">{{ $badge->name }}</div>
                <div style="font-size: 0.82rem; opacity: 0.9;">{{ $badge->description }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Botões de ação ── --}}
    <div class="d-flex flex-column gap-3 mt-2">
        <a href="{{ route('dashboard') }}" class="btn-purple w-100 text-center py-3">
            Voltar ao Dashboard
        </a>
        <a href="{{ route('ranking.index') }}" class="btn btn-outline-secondary w-100 rounded-3">
            Ver Ranking Semanal 🏆
        </a>
    </div>

</div>
</div>
@endsection
