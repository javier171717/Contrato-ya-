<?php helper('contrato') ?>
<nav class="finanzas-nav mb-4">
    <a href="<?= base_url('finanzas/gastos') ?>" class="finanzas-pill <?= ($seccion ?? '') === 'gastos' ? 'active' : '' ?>">
        <span class="finanzas-pill-icon">💵</span> Gastos
    </a>
    <a href="<?= base_url('finanzas/cierre') ?>" class="finanzas-pill <?= ($seccion ?? '') === 'cierre' ? 'active' : '' ?>">
        <span class="finanzas-pill-icon">💰</span> Cierre caja
    </a>
    <a href="<?= base_url('finanzas') ?>" class="finanzas-pill <?= ($seccion ?? '') === 'finanzas' ? 'active' : '' ?>">
        <span class="finanzas-pill-icon">📋</span> Finanzas
    </a>
</nav>
