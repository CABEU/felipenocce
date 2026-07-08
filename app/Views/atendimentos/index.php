<div class="page-head">
    <div>
        <h1>Atendimentos</h1>
        <p class="subtitle">Registro e acompanhamento dos atendimentos acadêmicos.</p>
    </div>
    <a class="btn btn-success" href="<?= e(url('frontend', 'atendimento_form')) ?>">Novo atendimento</a>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['tipo'] === 'erro' ? 'danger' : 'success' ?>">
        <?= e($flash['mensagem']) ?>
    </div>
<?php endif; ?>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pessoa</th>
                <th>Tipo</th>
                <th>Responsável</th>
                <th>Data</th>
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($atendimentos as $at): ?>
                <tr>
                    <td><?= (int) $at['id'] ?></td>
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
                    <td class="actions">
                        <a class="btn btn-sm btn-outline-primary"
                           href="<?= e(url('frontend', 'atendimento_status', ['id' => $at['id']])) ?>">Status</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
