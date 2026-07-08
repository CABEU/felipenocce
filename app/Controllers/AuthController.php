<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Atendimento;
use App\Models\Pessoa;
use App\Models\Tipo;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('auth', 'dashboard');
        }

        $erro = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            $usuario = Usuario::porEmail($email);
            if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
                Auth::login($usuario);
                $this->redirect('auth', 'dashboard');
            }
            $erro = 'E-mail ou senha inválidos.';
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->bare('auth/login', [
            'erro'  => $erro,
            'flash' => $flash,
        ], 'Entrar');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->flash('sucesso', 'Sessão encerrada com sucesso.');
        $this->redirect('auth', 'login');
    }

    public function dashboard(): void
    {
        Auth::requireLogin();

        $this->view('dashboard/index', [
            'totalPessoas'      => Pessoa::total(),
            'totalTipos'        => Tipo::total(),
            'totalAtendimentos' => Atendimento::total(),
            'recentes'          => Atendimento::todos(5),
        ], 'Dashboard');
    }
}
