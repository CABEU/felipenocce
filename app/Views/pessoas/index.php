<div class="page-head">
    <div>
        <h1>Pessoas atendidas</h1>
        <p class="subtitle">Cadastro, edição e inativação sem excluir o histórico.</p>
    </div>
    <a class="btn btn-success" href="<?= e(url('frontend', 'pessoa_form')) ?>">Nova pessoa</a>
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
                <th>Nome</th>
                <th>Documento</th>
                <th>E-mail</th>
                <th>Curso</th>
                <th>Período</th>
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pessoas as $p): ?>
                <tr>
                    <td><?= e($p['nome']) ?></td>
                    <td><?= e($p['documento']) ?></td>
                    <td><?= e($p['email']) ?></td>
                    <td><?= e($p['curso']) ?></td>
                    <td><?= e($p['periodo']) ?></td>
                    <td>
                        <?php if ($p['ativo']): ?>
                            <span class="badge badge-green">ativo</span>
                        <?php else: ?>
                            <span class="badge badge-gray">inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a class="btn btn-sm btn-outline-primary"
                           href="<?= e(url('frontend', 'pessoa_form', ['id' => $p['id']])) ?>">Editar</a>
                        <?php if ($p['ativo']): ?>
                            <a class="btn btn-sm btn-outline-danger"
                               href="<?= e(url('frontend', 'pessoa_status', ['id' => $p['id']])) ?>">Inativar</a>
                        <?php else: ?>
                            <a class="btn btn-sm btn-outline-success"
                               href="<?= e(url('frontend', 'pessoa_status', ['id' => $p['id']])) ?>">Ativar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
