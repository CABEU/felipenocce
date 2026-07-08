<div class="page-head">
    <div>
        <h1>Novo atendimento</h1>
        <p class="subtitle">Registro e acompanhamento dos atendimentos acadêmicos.</p>
    </div>
    <a class="btn btn-outline-primary" href="<?= e(url('frontend', 'atendimentos')) ?>">Voltar</a>
</div>

<div class="card" style="max-width: 720px;">
    <form method="post" action="<?= e(url('frontend', 'atendimento_salvar')) ?>">
        <div class="form-group">
            <label for="pessoa_id">Pessoa</label>
            <select id="pessoa_id" name="pessoa_id" class="form-control" required>
                <option value="">Selecione...</option>
                <?php foreach ($pessoas as $p): ?>
                    <option value="<?= (int) $p['id'] ?>"><?= e($p['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="tipo_id">Tipo de atendimento</label>
            <select id="tipo_id" name="tipo_id" class="form-control" required>
                <option value="">Selecione...</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= (int) $t['id'] ?>"><?= e($t['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="data">Data</label>
            <input type="date" id="data" name="data" class="form-control" value="<?= e($hoje) ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="aberto">aberto</option>
                <option value="concluido">concluido</option>
            </select>
        </div>
        <div class="form-group">
            <label for="observacao">Observação</label>
            <textarea id="observacao" name="observacao" class="form-control"></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Salvar</button>
            <a class="btn btn-outline-primary" href="<?= e(url('frontend', 'atendimentos')) ?>">Cancelar</a>
        </div>
    </form>
</div>
