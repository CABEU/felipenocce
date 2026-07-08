<?php
use App\Core\Auth;

$usuario = Auth::user();
$c = $_GET['controller'] ?? '';
$a = $_GET['action'] ?? '';

$isActive = function (array $actions) use ($a): string {
    return in_array($a, $actions, true) ? 'active' : '';
};
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="<?= e(base_url()) ?>assets/css/style.css">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <a class="brand" href="<?= e(url('auth', 'dashboard')) ?>">AtendeLab</a>
            <nav>
                <a class="<?= $isActive(['dashboard']) ?>" href="<?= e(url('auth', 'dashboard')) ?>">Dashboard</a>
                <a class="<?= $isActive(['pessoas', 'pessoa_form']) ?>" href="<?= e(url('frontend', 'pessoas')) ?>">Pessoas</a>
                <a class="<?= $isActive(['tipos', 'tipo_form']) ?>" href="<?= e(url('frontend', 'tipos')) ?>">Tipos</a>
                <a class="<?= $isActive(['atendimentos', 'atendimento_form']) ?>" href="<?= e(url('frontend', 'atendimentos')) ?>">Atendimentos</a>
            </nav>
            <div class="user">
                <span><?= e($usuario['nome'] ?? '') ?> &middot; <?= e($usuario['login'] ?? '') ?></span>
                <a class="btn-logout" href="<?= e(url('auth', 'logout')) ?>">Sair</a>
            </div>
        </div>
    </header>

    <main class="page">
        <div class="container">
            <?= $content ?>
        </div>
    </main>
</body>
</html>
