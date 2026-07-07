<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

/**
 * Controller base: conexão PDO e helpers de resposta/validação
 * compartilhados por todos os controllers de dados (JSON).
 */
abstract class BaseController
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = conexaoBanco();
    }

    protected function responderJson(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function responderErro(string $mensagem, int $status = 400): void
    {
        $this->responderJson(['erro' => $mensagem, 'mensagem' => $mensagem], $status);
    }

    protected function corpoRequisicao(): array
    {
        return $_POST;
    }

    protected function inteiroObrigatorio($valor, string $campo): int
    {
        if ($valor === null || $valor === '' || !is_numeric($valor)) {
            $this->responderErro("O campo {$campo} é obrigatório e deve ser numérico.", 422);
        }
        return (int) $valor;
    }

    protected function textoObrigatorio($valor, string $campo): string
    {
        $valor = trim((string) ($valor ?? ''));
        if ($valor === '') {
            $this->responderErro("O campo {$campo} é obrigatório.", 422);
        }
        return $valor;
    }

    protected function textoOpcional($valor): ?string
    {
        $valor = trim((string) ($valor ?? ''));
        return $valor === '' ? null : $valor;
    }

    protected function statusValido($valor, array $permitidos, string $padrao): string
    {
        return in_array($valor, $permitidos, true) ? $valor : $padrao;
    }
}
