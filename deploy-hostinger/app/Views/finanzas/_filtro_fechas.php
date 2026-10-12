<?php
/** @var string $action URL del formulario */
/** @var string $desde */
/** @var string $hasta */
?>
<form method="get" action="<?= esc($action) ?>" class="card border-0 shadow-sm mb-4 finanzas-filtro">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Desde</label>
                <input type="date" name="desde" class="form-control" value="<?= esc($desde) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= esc($hasta) ?>" required>
            </div>
            <div class="col-md-6 d-flex flex-wrap gap-2 align-items-end">
                <button type="submit" class="btn btn-success">Consultar</button>
                <a href="<?= esc($action) ?>?desde=<?= date('Y-m-d') ?>&hasta=<?= date('Y-m-d') ?>" class="btn btn-outline-secondary btn-sm">Hoy</a>
                <a href="<?= esc($action) ?>?desde=<?= date('Y-m-01') ?>&hasta=<?= date('Y-m-d') ?>" class="btn btn-outline-secondary btn-sm">Este mes</a>
                <a href="<?= esc($action) ?>?desde=<?= date('Y-m-01', strtotime('first day of last month')) ?>&hasta=<?= date('Y-m-t', strtotime('last day of last month')) ?>" class="btn btn-outline-secondary btn-sm">Mes anterior</a>
            </div>
        </div>
        <?php if (!empty($etiqueta_periodo)): ?>
            <p class="text-muted small mb-0 mt-3">
                Mostrando resultados: <strong><?= esc($etiqueta_periodo) ?></strong>
            </p>
        <?php endif; ?>
    </div>
</form>
