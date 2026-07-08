<?php

namespace App\Core;

/**
 * Controller base: renderização de views com layout.
 */
abstract class Controller
{
    /** Renderiza uma view dentro do layout padrão. */
    protected function view(string $template, array $data = [], ?string $title = null): void
    {
        extract($data, EXTR_SKIP);
        $title = $title ? "{$title} | AtendeLab" : 'AtendeLab';

        $viewFile = APP_PATH . '/Views/' . $template . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            exit("View não encontrada: {$template}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require APP_PATH . '/Views/layout/main.php';
    }

    /** View sem layout (ex.: login). */
    protected function bare(string $template, array $data = [], ?string $title = null): void
    {
        extract($data, EXTR_SKIP);
        $title = $title ? "{$title} | AtendeLab" : 'AtendeLab';
        require APP_PATH . '/Views/' . $template . '.php';
    }

    protected function redirect(string $controller, string $action = 'index', array $params = []): void
    {
        header('Location: ' . url($controller, $action, $params));
        exit;
    }

    /** Mensagem flash exibida na próxima requisição. */
    protected function flash(string $tipo, string $mensagem): void
    {
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
    }
}
