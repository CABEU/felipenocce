<div class="page-head">
    <div>
        <h1>Tipos de atendimento</h1>
        <p class="subtitle">Categorias utilizadas nos registros de atendimento.</p>
    </div>
    <a class="btn btn-success" href="<?= e(url('frontend', 'tipo_form')) ?>">Novo tipo</a>
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
                <th>Descrição</th>
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tipos as $t): ?>
                <tr>
                    <td><?= e($t['nome']) ?></td>
                    <td><?= e($t['descricao']) ?></td>
                    <td>
                        <?php if ($t['ativo']): ?>
                            <span class="badge badge-green">ativo</span>
                        <?php else: ?>
                            <span class="badge badge-gray">inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a class="btn btn-sm btn-outline-primary"
                           href="<?= e(url('frontend', 'tipo_form', ['id' => $t['id']])) ?>">Editar</a>
                        <?php if ($t['ativo']): ?>
                            <a class="btn btn-sm btn-outline-danger"
                               href="<?= e(url('frontend', 'tipo_status', ['id' => $t['id']])) ?>">Inativar</a>
                        <?php else: ?>
                            <a class="btn btn-sm btn-outline-success"
                               href="<?= e(url('frontend', 'tipo_status', ['id' => $t['id']])) ?>">Ativar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
