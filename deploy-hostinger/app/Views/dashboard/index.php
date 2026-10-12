<?= $this->extend('layouts/main') ?>
<?php helper('contrato') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-speedometer2 text-success"></i> Panel de Control</h1>
    <a href="<?= base_url('contratos/crear') ?>" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nuevo Contrato
    </a>
</div>

<div class="row g-3 mb-4">
    <?php $activos = ($stats['vigente'] ?? 0) + ($stats['renovado'] ?? 0); ?>
    <div class="col-6 col-md">
        <a href="<?= base_url('contratos?estado=activo') ?>" class="stat-card-link">
            <div class="stat-card stat-vigente">
                <div class="stat-number"><?= $activos ?></div>
                <div class="stat-label">Activos</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="<?= base_url('contratos?estado=redimido') ?>" class="stat-card-link">
            <div class="stat-card stat-redimido">
                <div class="stat-number"><?= $stats['redimido'] ?></div>
                <div class="stat-label">Redimidos</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="<?= base_url('contratos?estado=vencido') ?>" class="stat-card-link">
            <div class="stat-card stat-vencido">
                <div class="stat-number"><?= $stats['vencido'] ?></div>
                <div class="stat-label">Vencidos</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md">
        <a href="<?= base_url('contratos?estado=vendido') ?>" class="stat-card-link">
            <div class="stat-card stat-vendido">
                <div class="stat-number"><?= $stats['vendido'] ?></div>
                <div class="stat-label">Vendidos</div>
            </div>
        </a>
    </div>
</div>

<?php if (!empty($proximos)): ?>
<div class="card mb-4 border-warning">
    <div class="card-header bg-warning bg-opacity-25">
        <i class="bi bi-exclamation-triangle text-warning"></i>
        <strong>Por vencer esta semana</strong>
        <span class="text-muted small ms-1">(hoy y próximos 7 días)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th>Vence</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proximos as $c): ?>
                    <tr>
                        <td><strong><?= $c['numero'] ?></strong></td>
                        <td><?= esc($c['cliente_nombre']) ?></td>
                        <td><?= formato_moneda($c['monto']) ?></td>
                        <td>
                            <span class="badge bg-warning text-dark" title="<?= esc(formato_fecha_largo($c['fecha_terminacion'])) ?>">
                                <?= esc(etiqueta_vencimiento_relativa($c['fecha_terminacion'])) ?>
                            </span>
                            <div class="small text-muted mt-1"><?= formato_fecha_largo($c['fecha_terminacion']) ?></div>
                        </td>
                        <td>
                            <a href="<?= base_url('contratos/ver/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-clock-history"></i> Contratos Recientes</strong>
        <a href="<?= base_url('contratos') ?>" class="btn btn-sm btn-outline-success">Ver todos</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recientes)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox display-4"></i>
                <p class="mt-2">No hay contratos registrados aún.</p>
                <a href="<?= base_url('contratos/crear') ?>" class="btn btn-success">Crear primer contrato</a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Cliente</th>
                        <th>Artículo</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recientes as $c): ?>
                    <tr>
                        <td><strong><?= $c['numero'] ?></strong></td>
                        <td><?= esc($c['cliente_nombre']) ?></td>
                        <td class="text-truncate" style="max-width:150px"><?= esc($c['descripcion_articulo']) ?></td>
                        <td><?= formato_moneda($c['monto']) ?></td>
                        <td><span class="badge estado-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span></td>
                        <td>
                            <a href="<?= base_url('contratos/imprimir/' . $c['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Imprimir">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
