<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - StudyRank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #a020f0, #0d6efd);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', Arial, sans-serif;
            padding: 30px 0;
        }
        .card-login {
            background: white;
            padding: 40px 35px;
            border-radius: 20px;
            width: 420px;
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
        .link-login { color: #a020f0; font-weight: 600; text-decoration: none; }
        .link-login:hover { text-decoration: underline; }
        .invalid-feedback { text-align: left; }
        .form-text { text-align: left; }
    </style>
</head>
<body>

<div class="card-login">
    <div style="font-size: 40px; margin-bottom: 8px;">🏆</div>
    <h3 class="fw-bold mb-1">StudyRank</h3>
    <p style="color: #888; font-size: 0.9rem; margin-bottom: 28px;">Crie sua conta e comece a competir!</p>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        {{-- Nome completo --}}
        <div class="mb-3 text-start">
            <label for="name" class="form-label fw-semibold small">Nome completo</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                class="form-control @error('name') is-invalid @enderror"
                placeholder="Seu nome"
                required
                autofocus
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{--
            Nickname — campo extra que existe na nossa tabela users.
            O Laravel padrão não tem esse campo, portanto precisamos
            garantir que o AuthController o processe no register().
            Será o nome exibido publicamente no ranking e perfil.
        --}}
        <div class="mb-3 text-start">
            <label for="nickname" class="form-label fw-semibold small">Nickname</label>
            <input
                type="text"
                id="nickname"
                name="nickname"
                value="{{ old('nickname') }}"
                class="form-control @error('nickname') is-invalid @enderror"
                placeholder="Como quer ser chamado no ranking?"
                required
            >
            @error('nickname')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text text-muted" style="font-size: 0.78rem;">
                Visível publicamente no ranking. Sem espaços.
            </div>
        </div>

        {{-- Email --}}
        <div class="mb-3 text-start">
            <label for="email" class="form-label fw-semibold small">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="seu@email.com"
                required
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Senha --}}
        <div class="mb-3 text-start">
            <label for="password" class="form-label fw-semibold small">Senha</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Mínimo 8 caracteres"
                required
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{--
            password_confirmation — o Laravel valida automaticamente
            que este campo é igual ao campo 'password' quando usamos
            a regra 'confirmed' no controller/form request.
        --}}
        <div class="mb-4 text-start">
            <label for="password_confirmation" class="form-label fw-semibold small">
                Confirmar senha
            </label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control"
                placeholder="Repita a senha"
                required
            >
        </div>

        <button type="submit" class="btn-gradient">Criar Conta</button>
    </form>

    <div class="mt-4">
        <a href="{{ route('login') }}" class="link-login">Já tem conta? Entre aqui</a>
    </div>
</div>

</body>
</html>
