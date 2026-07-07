<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';

/**
 * Retorna os indicadores consolidados usados pelo dashboard e,
 * opcionalmente, pelos cards e por uma futura tabela de recentes.
 */
class DashboardController extends BaseController
{
    public function resumo(): void
    {
        $totalPessoas = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM pessoas WHERE status = 'ativo'")
            ->fetchColumn();

        $totalTipos = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM tipos_atendimentos WHERE status = 'ativo'")
            ->fetchColumn();

        $totalAtendimentos = (int) $this->pdo
            ->query('SELECT COUNT(*) FROM atendimentos')
            ->fetchColumn();

        $porStatus = $this->pdo
            ->query('SELECT status, COUNT(*) AS total FROM atendimentos GROUP BY status')
            ->fetchAll();

        $recentes = $this->pdo->query(
            "SELECT a.id, a.status, a.data_atendimento, a.horario_atendimento,
                    p.nome AS pessoa, t.nome AS tipo, u.nome AS responsavel
             FROM atendimentos a
             INNER JOIN pessoas p ON p.id = a.pessoa_id
             INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
             INNER JOIN usuarios u ON u.id = a.usuario_id
             ORDER BY a.data_atendimento DESC, a.horario_atendimento DESC
             LIMIT 5"
        )->fetchAll();

        $this->responderJson([
            'indicadores' => [
                'total_pessoas'      => $totalPessoas,
                'total_tipos'        => $totalTipos,
                'total_atendimentos' => $totalAtendimentos,
                'por_status'         => $porStatus,
            ],
            'atendimentos_recentes' => $recentes,
        ]);
    }
}
