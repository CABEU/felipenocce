<?php $editando = !empty($tipo); ?>
<div class="page-head">
    <div>
        <h1><?= $editando ? 'Editar tipo' : 'Novo tipo' ?></h1>
        <p class="subtitle">Categorias utilizadas nos registros de atendimento.</p>
    </div>
    <a class="btn btn-outline-primary" href="<?= e(url('frontend', 'tipos')) ?>">Voltar</a>
</div>

<div class="card" style="max-width: 720px;">
    <form method="post" action="<?= e(url('frontend', 'tipo_salvar')) ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= (int) $tipo['id'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" class="form-control"
                   value="<?= e($tipo['nome'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control"><?= e($tipo['descricao'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Salvar</button>
            <a class="btn btn-outline-primary" href="<?= e(url('frontend', 'tipos')) ?>">Cancelar</a>
        </div>
    </form>
</div>
