<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // =========================================================================
    // LOGIN
    // =========================================================================

    /**
     * Exibe a tela de login.
     * Se o usuário já está logado, redireciona direto para o dashboard.
     */
    public function showLogin()
    {
        // guest() é o middleware inverso de auth()
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }


    /**
     * Processa o formulário de login.
     *
     * O Laravel tenta encontrar um usuário com o email informado
     * e verificar se a senha bate (bcrypt).
     */


    public function login(Request $request)
    {
        dd('cheguei no controller');
        // 1. Validação dos campos obrigatórios
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Tenta autenticar
        // Auth::attempt() faz o hash da senha e compara com o banco
        if (Auth::attempt($credentials)) {
            // Regenera o ID de sessão para evitar session fixation
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        // 3. Falhou — retorna com erro no campo de email
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email ou senha incorretos.']);
    }

    // =========================================================================
    // REGISTER
    // =========================================================================

    /**
     * Exibe a tela de cadastro.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Processa o formulário de cadastro.
     *
     * Cria o usuário com os valores padrão de gamificação
     * (xp_total=0, level=1, current_streak=0) e faz login automático.
     */
    public function register(Request $request)
    {
        // 1. Validação completa
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            // nickname: único na tabela, sem espaços
            'nickname' => ['required', 'string', 'max:30', 'unique:users,nickname', 'alpha_num'],
            'email'    => ['required', 'email', 'unique:users,email'],
            // Password::defaults() exige mínimo 8 caracteres por convenção do Laravel
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            // Mensagens de erro em português
            'nickname.alpha_num' => 'O nickname só pode conter letras e números, sem espaços.',
            'nickname.unique'    => 'Este nickname já está em uso. Escolha outro.',
            'email.unique'       => 'Este email já está cadastrado.',
            'password.confirmed' => 'As senhas não coincidem.',
        ]);

        // 2. Cria o usuário
        // Os valores de gamificação já têm defaults na migration,
        // mas deixamos explícito aqui para clareza.
        $user = User::create([
            'name'               => $request->name,
            'nickname'           => $request->nickname,
            'email'              => $request->email,
            'password'           => $request->password, // O cast 'hashed' no model faz o bcrypt
            'xp_total'           => 0,
            'level'              => 1,
            'current_streak'     => 0,
            'last_activity_date' => null,
        ]);

        // 3. Dispara o evento de registro (para verificação de email se necessário)
        event(new Registered($user));

        // 4. Faz login automático após o cadastro
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', "Bem-vindo ao StudyRank, {$user->nickname}! 🎉 Comece completando um quiz.");
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================

    /**
     * Encerra a sessão do usuário.
     * O form no layout usa method POST + @csrf para segurança.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalida a sessão e regenera o token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
