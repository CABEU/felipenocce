<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

/**
 * Controller de autenticação: exibirLogin(), entrar(), dashboard() e logout().
 * As páginas são renderizadas em HTML (não em JSON).
 */
class AuthController
{
    private PDO $pdo;
    private string $baseUrl = '/atendelab/public/';

    public function __construct()
    {
        $this->pdo = conexaoBanco();
    }

    public function exibirLogin(): void
    {
        if (!empty($_SESSION['usuario']['id'])) {
            $this->redirecionarDashboard();
        }

        $mensagem  = isset($_GET['mensagem']) ? (string) $_GET['mensagem'] : null;
        $erroLogin = isset($_GET['erro']) ? (string) $_GET['erro'] : null;
        $baseUrl   = $this->baseUrl;

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function entrar(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');

        if ($email === '' || $senha === '') {
            $this->voltarComErro('Informe e-mail e senha.');
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, nome, email, senha, perfil, status FROM usuarios WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario || $usuario['status'] !== 'ativo' || !password_verify($senha, $usuario['senha'])) {
            $this->voltarComErro('E-mail ou senha incorretos.');
        }

        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'     => (int) $usuario['id'],
            'nome'   => $usuario['nome'],
            'email'  => $usuario['email'],
            'perfil' => $usuario['perfil'],
        ];

        $this->redirecionarDashboard();
    }

    public function dashboard(): void
    {
        if (empty($_SESSION['usuario']['id'])) {
            header('Location: ' . $this->baseUrl . '?controller=auth&action=login');
            exit;
        }

        $baseUrl = $this->baseUrl;
        require __DIR__ . '/../Views/dashboard/index.php';
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        session_destroy();
        header('Location: ' . $this->baseUrl . '?controller=auth&action=login');
        exit;
    }

    private function voltarComErro(string $mensagem): void
    {
        header('Location: ' . $this->baseUrl . '?controller=auth&action=login&erro=' . urlencode($mensagem));
        exit;
    }

    private function redirecionarDashboard(): void
    {
        header('Location: ' . $this->baseUrl . '?controller=auth&action=dashboard');
        exit;
    }
}
