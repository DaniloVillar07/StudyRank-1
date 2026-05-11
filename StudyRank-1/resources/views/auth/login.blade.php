<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - StudyRank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #a020f0, #0d6efd);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', Arial, sans-serif;
        }
        .card-login {
            background: white;
            padding: 40px 35px;
            border-radius: 20px;
            width: 400px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        .form-control {
            border-radius: 10px !important;
            padding: 12px 15px;
            border: 1.5px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #a020f0;
            box-shadow: 0 0 0 3px rgba(160,32,240,0.1);
        }
        .btn-gradient {
            background: linear-gradient(90deg, #a020f0, #0d6efd);
            border: none;
            color: white;
            border-radius: 12px;
            padding: 12px;
            width: 100%;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-gradient:hover { opacity: 0.9; }
        .link-register { color: #a020f0; font-weight: 600; text-decoration: none; }
        .link-register:hover { text-decoration: underline; }
        .footer-icons { font-size: 13px; color: #888; margin-top: 15px; }
        .invalid-feedback { text-align: left; }
    </style>
</head>
<body>

<div class="card-login">
    <div style="font-size: 40px; margin-bottom: 8px;">🏆</div>
    <h3 class="fw-bold mb-1">StudyRank</h3>
    <p style="color: #888; font-size: 0.9rem; margin-bottom: 28px;">Aprenda, compita e evolua!</p>

    {{--
        action: aponta para a rota 'login' definida em web.php
        method: POST (o Laravel espera POST para autenticação)
        @csrf: gera o token de segurança — NUNCA esqueça isso em forms POST
    --}}
    <form action="{{ route('login') }}" method="POST">
        @csrf

        {{-- Campo Email --}}
        <div class="mb-3 text-start">
            <label for="email" class="form-label fw-semibold small">Email</label>
            {{--
                old('email') preserva o valor digitado caso a validação falhe,
                para o usuário não precisar redigitar tudo.
                @error('email') exibe a mensagem de erro do Laravel abaixo do campo.
            --}}
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="seu@email.com"
                required
                autofocus
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Senha --}}
        <div class="mb-4 text-start">
            <label for="password" class="form-label fw-semibold small">Senha</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="••••••••"
                required
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-gradient">Entrar</button>
    </form>

    <div class="mt-4">
        {{-- route('register') aponta para a rota de cadastro --}}
        <a href="{{ route('register') }}" class="link-register">
            Não tem conta? Cadastre-se
        </a>
    </div>

    <hr style="margin: 20px 0; border-color: #eee;">
    <div class="footer-icons">🎮 5 Desafios &nbsp;•&nbsp; 🏅 Badges &nbsp;•&nbsp; 📊 Ranking Semanal</div>
</div>

</body>
</html>
