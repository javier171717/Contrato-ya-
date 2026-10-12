<?= $this->extend('layouts/main') ?>
<?php helper(['contrato', 'auth']) ?>

<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Gastos</h1>

<?= view('finanzas/_nav') ?>

<?= view('finanzas/_filtro_fechas', [
    'action' => base_url('finanzas/gastos'),
    'desde' => $desde,
    'hasta' => $hasta,
    'etiqueta_periodo' => $etiqueta_periodo,
]) ?>

<button type="button" class="btn btn-finanzas mb-4" data-bs-toggle="modal" data-bs-target="#modalGasto">
    Registrar gasto
</button>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($gastos)): ?>
            <div class="text-center py-5 text-muted">No hay gastos registrados.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Registró</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gastos as $g): ?>
                    <tr>
                        <td><?= esc($g['fecha']) ?></td>
                        <td><?= esc($g['concepto']) ?></td>
                        <td class="fw-semibold text-danger"><?= formato_moneda($g['monto']) ?></td>
                        <td><?= esc($g['usuario_nombre'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="modalGasto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('finanzas/gastos/guardar') ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar gasto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Concepto</label>
                        <input type="text" name="concepto" class="form-control" placeholder="Ej: Gasolina, arriendo..." required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" name="monto" class="form-control" inputmode="numeric" autocomplete="off" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-finanzas">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelector('#modalGasto input[name="monto"]')?.addEventListener('input', function() {
    const digits = this.value.replace(/\D/g, '');
    this.value = digits ? parseInt(digits, 10).toLocaleString('es-CO') : '';
});
</script>
<?= $this->endSection() ?>
