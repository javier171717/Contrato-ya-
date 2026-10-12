<?= $this->extend('layouts/main') ?>
<?php helper('contrato') ?>

<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Cierre de caja</h1>

<?= view('finanzas/_nav') ?>

<?= view('finanzas/_filtro_fechas', [
    'action' => base_url('finanzas/cierre'),
    'desde' => $desde,
    'hasta' => $hasta,
    'etiqueta_periodo' => $etiqueta_periodo,
]) ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <p class="text-muted mb-4">
            Cuadre el efectivo con los <strong>cobros de abonos</strong> del período menos los <strong>gastos</strong>
            y el dinero entregado en <strong>nuevos empeños</strong>.
            <?php if ($desde !== $hasta): ?>
                Para un solo día de cierre, use la misma fecha en <em>Desde</em> y <em>Hasta</em>.
            <?php endif; ?>
        </p>
        <div class="row g-4 finanzas-metricas">
            <div class="col-md-6 col-lg-3">
                <div class="fin-metric fin-metric-ingreso">
                    <span class="fin-metric-label">Ingresos (cobros)</span>
                    <span class="fin-metric-value"><?= formato_moneda($periodo['ingresos_efectivo']) ?></span>
                    <span class="fin-metric-sub">Abonos del período</span>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="fin-metric fin-metric-gasto">
                    <span class="fin-metric-label">Gastos pagados</span>
                    <span class="fin-metric-value"><?= formato_moneda($periodo['gastos']) ?></span>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="fin-metric fin-metric-empeño">
                    <span class="fin-metric-label">Empeños (salida)</span>
                    <span class="fin-metric-value"><?= formato_moneda($periodo['empeños']) ?></span>
                    <span class="fin-metric-sub"><?= (int) $periodo['empeños_count'] ?> uds</span>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="fin-metric fin-metric-balance">
                    <span class="fin-metric-label">Balance del período</span>
                    <span class="fin-metric-value"><?= formato_moneda($periodo['balance']) ?></span>
                    <span class="fin-metric-sub">Cobros − gastos − empeños</span>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-3 border-top">
            <span class="text-muted">Cartera por cobrar (actual):</span>
            <strong class="ms-2 fs-5 text-primary"><?= formato_moneda($periodo['cartera']) ?></strong>
            <a href="<?= base_url('contratos') ?>" class="ms-2 small">Ver contratos</a>
        </div>
        <div class="mt-3">
            <a href="<?= base_url('finanzas') ?>" class="text-decoration-none">Ver finanzas completas</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
