<?php $editando = !empty($pessoa); ?>
<div class="page-head">
    <div>
        <h1><?= $editando ? 'Editar pessoa' : 'Nova pessoa' ?></h1>
        <p class="subtitle">Preencha os dados da pessoa atendida.</p>
    </div>
    <a class="btn btn-outline-primary" href="<?= e(url('frontend', 'pessoas')) ?>">Voltar</a>
</div>

<div class="card" style="max-width: 720px;">
    <form method="post" action="<?= e(url('frontend', 'pessoa_salvar')) ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= (int) $pessoa['id'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" class="form-control"
                   value="<?= e($pessoa['nome'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="documento">Documento</label>
            <input type="text" id="documento" name="documento" class="form-control"
                   value="<?= e($pessoa['documento'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" class="form-control"
                   value="<?= e($pessoa['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="curso">Curso</label>
            <input type="text" id="curso" name="curso" class="form-control"
                   value="<?= e($pessoa['curso'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="periodo">Período</label>
            <input type="text" id="periodo" name="periodo" class="form-control"
                   value="<?= e($pessoa['periodo'] ?? '') ?>" placeholder="Ex.: 5º semestre">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Salvar</button>
            <a class="btn btn-outline-primary" href="<?= e(url('frontend', 'pessoas')) ?>">Cancelar</a>
        </div>
    </form>
</div>
