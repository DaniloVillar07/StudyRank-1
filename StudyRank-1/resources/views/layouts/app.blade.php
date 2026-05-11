<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- @yield('title') permite que cada view defina seu próprio título de aba --}}
    <title>@yield('title', 'StudyRank') - StudyRank</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --purple-main: #a020f0;
            --bg-light: #f8f9fa;
        }
        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        /* ── Navbar ── */
        .navbar {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 0.5rem 2rem;
        }
        .nav-link {
            font-weight: 500;
            color: #666;
            padding: 0.5rem 1.5rem !important;
            border-radius: 10px;
            transition: background 0.2s;
        }
        .nav-link:hover { background: #f3e8ff; color: var(--purple-main); }
        .nav-link.active {
            background-color: var(--purple-main);
            color: white !important;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
        }
        .avatar-box {
            background: #fdf2e9;
            width: 35px; height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Utilitários compartilhados ── */
        .progress { height: 12px; border-radius: 10px; background-color: #e9ecef; }
        .progress-bar { background: var(--purple-main); transition: width 0.5s; }
        .btn-purple {
            background: var(--purple-main);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-purple:hover { opacity: 0.85; color: white; }
        .btn-purple:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>

    {{-- Cada view pode injetar estilos próprios aqui --}}
    @yield('styles')
</head>
<body>

{{-- ════════════════════ NAVBAR ════════════════════ --}}
{{--
    Usamos request()->routeIs() para marcar o link ativo automaticamente,
    sem precisar passar variáveis extras do controller.
--}}
<nav class="navbar navbar-expand-lg mb-4">
    <div class="container-fluid">

        {{-- Logo --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <span style="font-size: 24px; margin-right: 10px;">🏆</span>
            <div>
                <b class="d-block" style="line-height: 1;">StudyRank</b>
                <small style="font-size: 10px; color: #888;">Aprenda e compita</small>
            </div>
        </a>

        {{-- Links centrais --}}
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav gap-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">🏠 Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ranking*') ? 'active' : '' }}"
                       href="{{ route('ranking.index') }}">🏆 Ranking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('perfil') ? 'active' : '' }}"
                       href="{{ route('perfil') }}">👤 Perfil</a>
                </li>
            </ul>
        </div>

        {{-- Info do usuário logado + logout --}}
        <div class="user-info">
            <div class="text-end">
                <div class="fw-bold">{{ auth()->user()->nickname }}</div>
                <div class="text-muted" style="font-size: 0.75rem;">
                    Nível {{ auth()->user()->level }} • {{ auth()->user()->xp_total }} XP
                </div>
            </div>
            <div class="avatar-box">👨‍💻</div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit"
                        class="btn btn-link text-muted text-decoration-none small p-0 ms-1">
                    Sair
                </button>
            </form>
        </div>

    </div>
</nav>
{{-- ════════════════════ FIM NAVBAR ════════════════════ --}}

{{-- Mensagens flash (sucesso, erro, info) enviadas pelo controller via redirect()->with() --}}
@if(session('success'))
    <div class="container">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="container">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="container">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

{{-- Conteúdo principal da página --}}
<main>
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Cada view pode injetar scripts próprios aqui --}}
@yield('scripts')

</body>
</html>
