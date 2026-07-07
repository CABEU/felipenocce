<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';

/**
 * CRUD de pessoas atendidas. Inativar nunca apaga o registro
 * (histórico de atendimentos depende de pessoa_id).
 */
class PessoasController extends BaseController
{
    public function listar(): void
    {
        $pessoas = $this->pdo
            ->query('SELECT * FROM pessoas ORDER BY nome ASC')
            ->fetchAll();

        $this->responderJson(['pessoas' => $pessoas]);
    }

    public function buscarPorId(): void
    {
        $id = $this->inteiroObrigatorio($_GET['id'] ?? null, 'id');

        $stmt = $this->pdo->prepare('SELECT * FROM pessoas WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $pessoa = $stmt->fetch();

        if (!$pessoa) {
            $this->responderErro('Pessoa não encontrada.', 404);
        }

        $this->responderJson(['pessoa' => $pessoa]);
    }

    public function criar(): void
    {
        $dados = $this->validarDados($this->corpoRequisicao());

        $this->garantirDocumentoUnico($dados['documento']);

        $stmt = $this->pdo->prepare(
            'INSERT INTO pessoas (nome, documento, telefone, email, curso, periodo, observacoes, status)
             VALUES (:nome, :documento, :telefone, :email, :curso, :periodo, :observacoes, :status)'
        );
        $stmt->execute($dados);

        $this->responderJson([
            'mensagem' => 'Pessoa cadastrada com sucesso.',
            'id'       => (int) $this->pdo->lastInsertId(),
        ], 201);
    }

    public function atualizar(): void
    {
        $corpo = $this->corpoRequisicao();
        $id    = $this->inteiroObrigatorio($corpo['id'] ?? null, 'id');
        $dados = $this->validarDados($corpo);

        $this->garantirDocumentoUnico($dados['documento'], $id);

        $dados['id'] = $id;

        $stmt = $this->pdo->prepare(
            'UPDATE pessoas SET
                nome = :nome, documento = :documento, telefone = :telefone,
                email = :email, curso = :curso, periodo = :periodo,
                observacoes = :observacoes, status = :status
             WHERE id = :id'
        );
        $stmt->execute($dados);

        $this->responderJson(['mensagem' => 'Pessoa atualizada com sucesso.']);
    }

    public function inativar(): void
    {
        $id = $this->inteiroObrigatorio($_POST['id'] ?? null, 'id');

        $stmt = $this->pdo->prepare("UPDATE pessoas SET status = 'inativo' WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $this->responderJson(['mensagem' => 'Pessoa inativada com sucesso.']);
    }

    private function validarDados(array $dados): array
    {
        return [
            'nome'        => $this->textoObrigatorio($dados['nome'] ?? null, 'nome'),
            'documento'   => $this->textoObrigatorio($dados['documento'] ?? null, 'documento'),
            'telefone'    => $this->textoOpcional($dados['telefone'] ?? null),
            'email'       => $this->textoObrigatorio($dados['email'] ?? null, 'email'),
            'curso'       => $this->textoOpcional($dados['curso'] ?? null),
            'periodo'     => $this->textoOpcional($dados['periodo'] ?? null),
            'observacoes' => $this->textoOpcional($dados['observacoes'] ?? null),
            'status'      => $this->statusValido($dados['status'] ?? 'ativo', ['ativo', 'inativo'], 'ativo'),
        ];
    }

    private function garantirDocumentoUnico(string $documento, ?int $ignorarId = null): void
    {
        $sql = 'SELECT id FROM pessoas WHERE documento = :documento';
        $parametros = ['documento' => $documento];

        if ($ignorarId !== null) {
            $sql .= ' AND id != :id';
            $parametros['id'] = $ignorarId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        if ($stmt->fetch()) {
            $this->responderErro('Já existe uma pessoa cadastrada com esse documento.', 422);
        }
    }
}
