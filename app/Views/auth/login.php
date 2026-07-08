<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="<?= e(base_url()) ?>assets/css/style.css">
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-logo">AL</div>
            <h1>AtendeLab</h1>
            <p class="subtitle">Controle de atendimentos acadêmicos</p>

            <?php if (!empty($flash) && $flash['tipo'] === 'sucesso'): ?>
                <div class="alert alert-success"><?= e($flash['mensagem']) ?></div>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger"><?= e($erro) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= e(url('auth', 'login')) ?>">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success btn-block btn-lg">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>
