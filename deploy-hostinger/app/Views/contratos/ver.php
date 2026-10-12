<?= $this->extend('layouts/main') ?>
<?php helper(['contrato', 'auth']) ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h3 mb-0">Contrato N° <?= $contrato['numero'] ?></h1>
    <div class="d-flex gap-2">
        <a href="<?= base_url('contratos/imprimir/' . $contrato['id']) ?>" class="btn btn-success">
            <i class="bi bi-printer"></i> Imprimir
        </a>
        <a href="<?= base_url('contratos') ?>" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <?php if (!empty($resumen['cerrado'])): ?>
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-check-circle-fill"></i>
                <?= $contrato['estado'] === 'vendido' ? 'Artículo vendido' : 'Artículo redimido' ?>
            </div>
            <div class="card-body">
                <p class="mb-3 fs-5">
                    <?php if ($contrato['estado'] === 'vendido'): ?>
                        El cliente no recuperó el artículo. Ya no está en inventario de la compraventa.
                    <?php else: ?>
                        El cliente pagó la totalidad y <strong>retiró su prenda</strong>. Este contrato está cerrado.
                    <?php endif; ?>
                </p>
                <div class="row g-3">
                    <div class="col-sm-4">
                        <span class="fin-label">Valor del empeño</span>
                        <div class="fw-bold"><?= formato_moneda($contrato['monto']) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <span class="fin-label">Saldo pendiente</span>
                        <div class="fw-bold text-success fs-4">$0</div>
                    </div>
                    <?php if ($fecha_cierre): ?>
                    <div class="col-sm-4">
                        <span class="fin-label">Fecha de cierre</span>
                        <div class="fw-bold"><?= formato_fecha_largo($fecha_cierre) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Resumen financiero activo -->
        <div class="card mb-4 border-success">
            <div class="card-header bg-success text-white">
                <i class="bi bi-cash-stack"></i> Estado financiero
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="fin-box">
                            <span class="fin-label">Capital (saldo)</span>
                            <span class="fin-value text-success"><?= formato_moneda($resumen['saldo_capital']) ?></span>
                            <?php if ($resumen['saldo_capital'] < $resumen['monto_original']): ?>
                                <small class="text-muted">Original: <?= formato_moneda($resumen['monto_original']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fin-box">
                            <span class="fin-label">Interés mensual (<?= number_format($resumen['tasa_mensual'], 0) ?>%)</span>
                            <span class="fin-value"><?= formato_moneda($resumen['interes_mensual']) ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fin-box">
                            <span class="fin-label">Interés devengado</span>
                            <span class="fin-value text-warning-emphasis"><?= formato_moneda($resumen['interes_devengado']) ?></span>
                            <small class="text-muted">
                                <?php if ($resumen['mes_minimo_aplicado']): ?>
                                    Mínimo 1 mes (<?= $resumen['dias_periodo'] ?> días)
                                <?php else: ?>
                                    <?= $resumen['dias_periodo'] ?> días (<?= $resumen['meses_periodo'] ?> meses)
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="fin-box fin-total">
                            <span class="fin-label">Total a pagar hoy</span>
                            <span class="fin-value fs-3 fw-bold text-danger"><?= formato_moneda($resumen['total_adeudado']) ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="fin-box">
                            <span class="fin-label">Período de interés</span>
                            <span><?= formato_fecha_largo($resumen['fecha_desde']) ?> → <?= formato_fecha_largo($resumen['fecha_corte']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Datos del contrato -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small">Vendedor</label>
                        <div class="fw-bold"><?= esc($contrato['cliente_nombre']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">C.C.</label>
                        <div class="fw-bold"><?= esc($contrato['cliente_cedula']) ?></div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Artículo</label>
                        <div class="fw-bold"><?= esc($contrato['descripcion_articulo']) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-muted small">Valor empeño</label>
                        <div class="fw-bold"><?= formato_moneda($contrato['monto']) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-muted small">Fecha inicio</label>
                        <div><?= formato_fecha_largo($contrato['fecha_inicio']) ?></div>
                    </div>
                    <?php if (empty($resumen['cerrado'])): ?>
                    <div class="col-sm-4">
                        <label class="text-muted small">Vigencia hasta</label>
                        <div class="fw-bold"><?= formato_fecha_largo($contrato['fecha_terminacion']) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-muted small">Días restantes</label>
                        <div>
                            <?php $dias = $resumen['dias_restantes']; ?>
                            <span class="badge <?= $dias < 0 ? 'bg-danger' : ($dias <= 7 ? 'bg-warning text-dark' : 'bg-success') ?>">
                                <?= $dias < 0 ? 'Vencido hace ' . abs($dias) . ' días' : $dias . ' días' ?>
                            </span>
                        </div>
                    </div>
                    <?php elseif ($fecha_cierre): ?>
                    <div class="col-sm-4">
                        <label class="text-muted small">Fecha de cierre</label>
                        <div class="fw-bold text-primary"><?= formato_fecha_largo($fecha_cierre) ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="col-sm-4">
                        <label class="text-muted small">Registrado por</label>
                        <div class="fw-bold">
                            <?= esc($contrato['creador_nombre'] ?? '—') ?>
                            <?php if (!empty($contrato['creador_rol'])): ?>
                                <span class="badge <?= $contrato['creador_rol'] === 'admin' ? 'bg-success' : 'bg-secondary' ?> ms-1">
                                    <?= etiqueta_rol_usuario($contrato['creador_rol']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($contrato['created_at'])): ?>
                                <span class="text-muted small fw-normal"> · <?= date('d/m/Y H:i', strtotime($contrato['created_at'])) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial abonos -->
        <?php if (!empty($abonos)): ?>
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history"></i> Historial de abonos</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Abono</th>
                                <th>Interés</th>
                                <th>Capital</th>
                                <th>Nuevo saldo</th>
                                <?php if (empty($resumen['cerrado'])): ?>
                                <th>Nueva vigencia</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($abonos as $a): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($a['fecha_abono'])) ?></td>
                                <td class="fw-semibold text-success"><?= formato_moneda($a['monto']) ?></td>
                                <td><?= formato_moneda($a['interes_cobrado']) ?></td>
                                <td><?= formato_moneda($a['capital_abonado']) ?></td>
                                <td><?= formato_moneda($a['saldo_total_nuevo']) ?></td>
                                <?php if (empty($resumen['cerrado'])): ?>
                                <td><?= date('d/m/Y', strtotime($a['fecha_terminacion_nueva'])) ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <?php if ($puede_abono): ?>
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-wallet2"></i> Registrar abono
            </div>
            <div class="card-body">
                <form method="post" action="<?= base_url('contratos/abono/' . $contrato['id']) ?>" id="formAbono">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Monto del abono</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">$</span>
                            <input type="text" name="monto" id="montoAbono" class="form-control"
                                   inputmode="numeric" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha del abono</label>
                        <input type="date" name="fecha_abono" id="fechaAbono" class="form-control"
                               value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div id="previewAbono" class="alert alert-light border d-none mb-3 small">
                        <div class="d-flex justify-content-between"><span>Interés del período:</span><strong id="pvInteres">—</strong></div>
                        <div class="d-flex justify-content-between"><span>Aplica a interés:</span><strong id="pvAInteres">—</strong></div>
                        <div class="d-flex justify-content-between"><span>Aplica a capital:</span><strong id="pvACapital">—</strong></div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between"><span>Nuevo saldo:</span><strong class="text-success" id="pvSaldo">—</strong></div>
                        <div class="d-flex justify-content-between"><span>Nueva vigencia:</span><strong id="pvVigencia">—</strong></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-check-circle"></i> Registrar abono
                    </button>
                </form>
                <p class="text-muted small mt-3 mb-0">
                    Se cobra mínimo <strong>1 mes de interés</strong> (<?= number_format($resumen['tasa_mensual'], 0) ?>%)
                    aunque el cliente venga el mismo día. El abono cubre primero intereses y el resto reduce capital.
                    Se renueva la vigencia <?= $resumen['plazo_dias'] ?> días.
                </p>
            </div>
        </div>
        <?php elseif (contrato_finalizado($contrato)): ?>
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <?php if ($contrato['estado'] === 'vendido'): ?>
                Artículo <strong>vendido</strong>. Ya no está en la compraventa.
            <?php else: ?>
                Artículo <strong>redimido</strong>. El cliente recuperó su prenda.
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">Estado del contrato</div>
            <div class="card-body">
                <p>Estado actual: <span class="badge estado-<?= $contrato['estado'] ?> fs-6"><?= ucfirst($contrato['estado']) ?></span></p>
                <?php if (es_dueño()): ?>
                <form action="<?= base_url('contratos/estado/' . $contrato['id']) ?>" method="post">
                    <select name="estado" class="form-select mb-2">
                        <option value="vigente" <?= $contrato['estado'] === 'vigente' ? 'selected' : '' ?>>Vigente</option>
                        <option value="renovado" <?= $contrato['estado'] === 'renovado' ? 'selected' : '' ?>>Renovado</option>
                        <option value="redimido" <?= $contrato['estado'] === 'redimido' ? 'selected' : '' ?>>Redimido</option>
                        <option value="vencido" <?= $contrato['estado'] === 'vencido' ? 'selected' : '' ?>>Vencido</option>
                        <option value="vendido" <?= $contrato['estado'] === 'vendido' ? 'selected' : '' ?>>Vendido</option>
                    </select>
                    <button type="submit" class="btn btn-outline-primary w-100">Actualizar estado</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if ($puede_abono): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const montoInput = document.getElementById('montoAbono');
    const fechaInput = document.getElementById('fechaAbono');
    const preview = document.getElementById('previewAbono');
    const urlSim = '<?= base_url('api/contratos/' . $contrato['id'] . '/simular-abono') ?>';
    let timeout;

    montoInput.addEventListener('input', function() {
        const digits = this.value.replace(/\D/g, '');
        this.value = digits ? parseInt(digits, 10).toLocaleString('es-CO') : '';
        clearTimeout(timeout);
        timeout = setTimeout(simular, 350);
    });

    fechaInput.addEventListener('change', simular);

    async function simular() {
        const monto = montoInput.value.replace(/\D/g, '');
        if (!monto || parseInt(monto, 10) <= 0) {
            preview.classList.add('d-none');
            return;
        }

        try {
            const params = new URLSearchParams({ monto: monto, fecha: fechaInput.value });
            const res = await fetch(urlSim + '?' + params);
            const data = await res.json();
            if (data.error) return;

            document.getElementById('pvInteres').textContent = data.formato.interes;
            document.getElementById('pvAInteres').textContent = formatCop(data.interes_cobrado);
            document.getElementById('pvACapital').textContent = formatCop(data.capital_abonado);
            document.getElementById('pvSaldo').textContent = data.redimido ? 'REDIMIDO' : data.formato.saldo_nuevo;
            document.getElementById('pvVigencia').textContent = data.formato.nueva_vigencia;
            preview.classList.remove('d-none');
        } catch (e) {}
    }

    function formatCop(n) {
        return '$' + parseInt(n, 10).toLocaleString('es-CO');
    }
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
