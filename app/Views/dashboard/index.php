<div class="page-head">
    <div>
        <h1>Dashboard</h1>
        <p class="subtitle">Resumo dos dados cadastrados no AtendeLab.</p>
    </div>
</div>

<div class="stats">
    <div class="stat">
        <div class="label">Pessoas cadastradas</div>
        <div class="value"><?= (int) $totalPessoas ?></div>
    </div>
    <div class="stat">
        <div class="label">Tipos de atendimento</div>
        <div class="value"><?= (int) $totalTipos ?></div>
    </div>
    <div class="stat">
        <div class="label">Atendimentos registrados</div>
        <div class="value"><?= (int) $totalAtendimentos ?></div>
    </div>
</div>

<div class="card">
    <h2>Acesso rápido</h2>
    <p class="muted">Use os módulos abaixo para cadastrar e consultar dados reais.</p>
    <div class="quick-actions">
        <a class="btn btn-success" href="<?= e(url('frontend', 'pessoas')) ?>">Gerenciar pessoas</a>
        <a class="btn btn-outline-success" href="<?= e(url('frontend', 'tipos')) ?>">Gerenciar tipos</a>
        <a class="btn btn-outline-success" href="<?= e(url('frontend', 'atendimentos')) ?>">Registrar atendimentos</a>
    </div>
</div>

<div class="card">
    <h2>Atendimentos recentes</h2>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Pessoa</th>
                    <th>Tipo</th>
                    <th>Responsável</th>
                    <th>Data</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentes)): ?>
                    <tr><td colspan="5" class="muted">Nenhum atendimento registrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentes as $at): ?>
                        <tr>
                            <td><?= e($at['pessoa_nome']) ?></td>
                            <td><?= e($at['tipo_nome']) ?></td>
                            <td><?= e($at['usuario_nome']) ?></td>
                            <td><?= e($at['data']) ?></td>
                            <td>
                                <?php if ($at['status'] === 'concluido'): ?>
                                    <span class="badge badge-green">concluido</span>
                                <?php else: ?>
                                    <span class="badge badge-blue">aberto</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
