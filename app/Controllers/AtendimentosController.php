<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';

/**
 * Registro e acompanhamento de atendimentos.
 * O responsável (usuario_id) sempre vem da sessão, nunca de um campo
 * livre do formulário - ver usuarioResponsavel().
 */
class AtendimentosController extends BaseController
{
    public function listar(): void
    {
        $sql = "SELECT a.id, a.pessoa_id, a.tipo_atendimento_id, a.usuario_id,
                       a.descricao, a.status, a.data_atendimento, a.horario_atendimento,
                       a.observacao_final,
                       p.nome AS pessoa, t.nome AS tipo, u.nome AS responsavel
                FROM atendimentos a
                INNER JOIN pessoas p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios u ON u.id = a.usuario_id
                ORDER BY a.data_atendimento DESC, a.horario_atendimento DESC";

        $atendimentos = $this->pdo->query($sql)->fetchAll();

        $this->responderJson(['atendimentos' => $atendimentos]);
    }

    public function visualizar(): void
    {
        $id = $this->inteiroObrigatorio($_GET['id'] ?? null, 'id');

        $stmt = $this->pdo->prepare(
            "SELECT a.*, p.nome AS pessoa, t.nome AS tipo, u.nome AS responsavel
             FROM atendimentos a
             INNER JOIN pessoas p ON p.id = a.pessoa_id
             INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
             INNER JOIN usuarios u ON u.id = a.usuario_id
             WHERE a.id = :id"
        );
        $stmt->execute(['id' => $id]);
        $atendimento = $stmt->fetch();

        if (!$atendimento) {
            $this->responderErro('Atendimento não encontrado.', 404);
        }

        $this->responderJson(['atendimento' => $atendimento]);
    }

    public function opcoesFormulario(): void
    {
        $pessoas = $this->pdo
            ->query("SELECT id, nome FROM pessoas WHERE status = 'ativo' ORDER BY nome")
            ->fetchAll();

        $tipos = $this->pdo
            ->query("SELECT id, nome FROM tipos_atendimentos WHERE status = 'ativo' ORDER BY nome")
            ->fetchAll();

        $this->responderJson(['pessoas' => $pessoas, 'tipos' => $tipos]);
    }

    public function criar(): void
    {
        $dados = $this->corpoRequisicao();

        $pessoaId  = $this->inteiroObrigatorio($dados['pessoa_id'] ?? null, 'pessoa_id');
        $tipoId    = $this->inteiroObrigatorio($dados['tipo_atendimento_id'] ?? null, 'tipo_atendimento_id');
        $descricao = $this->textoObrigatorio($dados['descricao'] ?? null, 'descricao');
        $data      = $this->textoObrigatorio($dados['data_atendimento'] ?? null, 'data_atendimento');
        $horario   = $this->textoObrigatorio($dados['horario_atendimento'] ?? null, 'horario_atendimento');
        $usuarioId = $this->usuarioResponsavel();

        $this->garantirAtivo('pessoas', $pessoaId, 'Pessoa');
        $this->garantirAtivo('tipos_atendimentos', $tipoId, 'Tipo de atendimento');

        $stmt = $this->pdo->prepare(
            'INSERT INTO atendimentos
                (pessoa_id, tipo_atendimento_id, usuario_id, descricao, status, data_atendimento, horario_atendimento)
             VALUES (:pessoa_id, :tipo_atendimento_id, :usuario_id, :descricao, :status, :data_atendimento, :horario_atendimento)'
        );
        $stmt->execute([
            'pessoa_id'           => $pessoaId,
            'tipo_atendimento_id' => $tipoId,
            'usuario_id'          => $usuarioId,
            'descricao'           => $descricao,
            'status'              => 'aberto',
            'data_atendimento'    => $data,
            'horario_atendimento' => $horario,
        ]);

        $this->responderJson([
            'mensagem' => 'Atendimento registrado com sucesso.',
            'id'       => (int) $this->pdo->lastInsertId(),
        ], 201);
    }

    public function atualizarStatus(): void
    {
        $dados = $this->corpoRequisicao();

        $id     = $this->inteiroObrigatorio($dados['id'] ?? null, 'id');
        $status = $this->textoObrigatorio($dados['status'] ?? null, 'status');

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->responderErro('Status inválido.', 422);
        }

        $observacaoFinal = $this->textoOpcional($dados['observacao_final'] ?? null);

        if ($status === 'concluido' && $observacaoFinal === null) {
            $this->responderErro('Informe a observação final para concluir o atendimento.', 422);
        }

        $stmt = $this->pdo->prepare(
            'UPDATE atendimentos SET status = :status, observacao_final = :observacao_final WHERE id = :id'
        );
        $stmt->execute([
            'status'           => $status,
            'observacao_final' => $observacaoFinal,
            'id'               => $id,
        ]);

        $this->responderJson(['mensagem' => 'Status atualizado com sucesso.']);
    }

    /**
     * O responsável pelo atendimento vem da sessão do usuário logado.
     * Só recorre ao campo usuario_id (ex.: testes via Thunder Client sem sessão)
     * quando não há sessão ativa.
     */
    private function usuarioResponsavel(): int
    {
        if (isset($_SESSION['usuario']['id'])) {
            return (int) $_SESSION['usuario']['id'];
        }

        return $this->inteiroObrigatorio($_POST['usuario_id'] ?? null, 'usuario_id');
    }

    private function garantirAtivo(string $tabela, int $id, string $rotulo): void
    {
        $tabelasPermitidas = ['pessoas', 'tipos_atendimentos'];
        if (!in_array($tabela, $tabelasPermitidas, true)) {
            $this->responderErro('Tabela inválida.', 500);
        }

        $stmt = $this->pdo->prepare("SELECT status FROM {$tabela} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $status = $stmt->fetchColumn();

        if ($status === false) {
            $this->responderErro("{$rotulo} não encontrada.", 404);
        }

        if ($status !== 'ativo') {
            $this->responderErro("{$rotulo} está inativa e não pode ser usada em um novo atendimento.", 422);
        }
    }
}
