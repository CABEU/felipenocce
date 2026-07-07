<?php
declare(strict_types=1);

/**
 * Middleware para endpoints de dados (JSON).
 * Usado pelos controllers pessoas, tipos, atendimentos e dashboard.
 */
function exigirAutenticacao(): void
{
    if (empty($_SESSION['usuario']['id'])) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode([
            'erro' => 'Usuário não autenticado. Faça login novamente.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Middleware para páginas visuais (redireciona para o login em vez de
 * responder JSON). Usado pelo FrontendController e por AuthController::dashboard().
 */
function exigirAutenticacaoPagina(): void
{
    if (empty($_SESSION['usuario']['id'])) {
        header('Location: /atendelab/public/?controller=auth&action=login');
        exit;
    }
}

function responderRotaNaoEncontrada(string $mensagem = 'Rota não encontrada.'): void
{
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(404);
    echo json_encode(['erro' => $mensagem], JSON_UNESCAPED_UNICODE);
    exit;
}
