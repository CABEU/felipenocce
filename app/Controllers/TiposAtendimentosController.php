<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';

/**
 * CRUD de tipos de atendimento (categorias usadas nos registros de atendimento).
 */
class TiposAtendimentosController extends BaseController
{
    public function listar(): void
    {
        $tipos = $this->pdo
            ->query('SELECT * FROM tipos_atendimentos ORDER BY nome ASC')
            ->fetchAll();

        $this->responderJson(['tipos' => $tipos]);
    }

    public function buscarPorId(): void
    {
        $id = $this->inteiroObrigatorio($_GET['id'] ?? null, 'id');

        $stmt = $this->pdo->prepare('SELECT * FROM tipos_atendimentos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $tipo = $stmt->fetch();

        if (!$tipo) {
            $this->responderErro('Tipo de atendimento não encontrado.', 404);
        }

        $this->responderJson(['tipo' => $tipo]);
    }

    public function criar(): void
    {
        $dados = $this->validarDados($this->corpoRequisicao());

        $stmt = $this->pdo->prepare(
            'INSERT INTO tipos_atendimentos (nome, descricao, status)
             VALUES (:nome, :descricao, :status)'
        );
        $stmt->execute($dados);

        $this->responderJson([
            'mensagem' => 'Tipo cadastrado com sucesso.',
            'id'       => (int) $this->pdo->lastInsertId(),
        ], 201);
    }

    public function atualizar(): void
    {
        $corpo = $this->corpoRequisicao();
        $id    = $this->inteiroObrigatorio($corpo['id'] ?? null, 'id');
        $dados = $this->validarDados($corpo);
        $dados['id'] = $id;

        $stmt = $this->pdo->prepare(
            'UPDATE tipos_atendimentos SET nome = :nome, descricao = :descricao, status = :status
             WHERE id = :id'
        );
        $stmt->execute($dados);

        $this->responderJson(['mensagem' => 'Tipo atualizado com sucesso.']);
    }

    public function inativar(): void
    {
        $id = $this->inteiroObrigatorio($_POST['id'] ?? null, 'id');

        $stmt = $this->pdo->prepare("UPDATE tipos_atendimentos SET status = 'inativo' WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $this->responderJson(['mensagem' => 'Tipo inativado com sucesso.']);
    }

    private function validarDados(array $dados): array
    {
        return [
            'nome'      => $this->textoObrigatorio($dados['nome'] ?? null, 'nome'),
            'descricao' => $this->textoOpcional($dados['descricao'] ?? null),
            'status'    => $this->statusValido($dados['status'] ?? 'ativo', ['ativo', 'inativo'], 'ativo'),
        ];
    }
}
