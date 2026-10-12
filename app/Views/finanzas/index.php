<?= $this->extend('layouts/main') ?>
<?php helper('contrato') ?>

<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Finanzas</h1>

<?= view('finanzas/_nav') ?>

<?= view('finanzas/_filtro_fechas', [
    'action' => base_url('finanzas'),
    'desde' => $desde,
    'hasta' => $hasta,
    'etiqueta_periodo' => $etiqueta_periodo,
]) ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Período seleccionado</div>
    <div class="card-body p-4">
        <div class="row g-4 finanzas-metricas">
            <div class="col-md-6 col-lg-3">
                <div class="fin-metric fin-metric-ingreso">
                    <span class="fin-metric-label">Ingresos (cobros)</span>
                    <span class="fin-metric-value"><?= formato_moneda($periodo['ingresos_efectivo']) ?></span>
                    <span class="fin-metric-sub">Abonos registrados</span>
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
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Resumen rápido</div>
    <div class="card-body p-4">
        <div class="row g-4 finanzas-metricas">
            <div class="col-md-6 col-lg-3">
                <div class="fin-metric fin-metric-cartera">
                    <span class="fin-metric-label">Cartera — por cobrar (hoy)</span>
                    <span class="fin-metric-value"><?= formato_moneda($hoy['cartera']) ?></span>
                    <a href="<?= base_url('contratos') ?>" class="fin-metric-sub">Ver contratos</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="fin-metric fin-metric-ingreso">
                    <span class="fin-metric-label">Hoy — Ingresos</span>
                    <span class="fin-metric-value"><?= formato_moneda($hoy['ingresos_efectivo']) ?></span>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="fin-metric fin-metric-balance">
                    <span class="fin-metric-label">Hoy — Balance</span>
                    <span class="fin-metric-value"><?= formato_moneda($hoy['balance']) ?></span>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="fin-metric fin-metric-balance">
                    <span class="fin-metric-label">Mes actual — Balance</span>
                    <span class="fin-metric-value"><?= formato_moneda($mes['balance']) ?></span>
                    <span class="fin-metric-sub">Sin filtro de fechas</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
