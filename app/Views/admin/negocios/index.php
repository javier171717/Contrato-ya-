<?= $this->extend('layouts/admin') ?>
<?php helper('contrato'); $nm = new \App\Models\NegocioModel(); ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <h1 class="admin-title mb-0">Negocios</h1>
    <a href="<?= base_url('admin/negocios/crear') ?>" class="btn-admin-gradient">
        <i class="bi bi-plus-lg"></i> Alta de negocio y dueño
    </a>
</div>

<div class="admin-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Plan</th>
                    <th>Estado panel</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($negocios)): ?>
                <tr><td colspan="4" class="text-center text-muted py-5">No hay negocios registrados.</td></tr>
                <?php else: ?>
                <?php foreach ($negocios as $n): ?>
                <tr>
                    <td>
                        <a href="<?= base_url('admin/negocios/ver/' . $n['id']) ?>" class="admin-link">
                            <?= esc($n['nombre']) ?>
                        </a>
                    </td>
                    <td><?= esc($nm->etiquetaPlan($n)) ?></td>
                    <td>
                        <span class="badge <?= $nm->puedeAccederPanel($n) ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger' ?>">
                            <?= esc($nm->etiquetaPanel($n)) ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <?php if ((int) $n['suspendido_admin'] === 1): ?>
                            <a href="<?= base_url('admin/negocios/toggle/' . $n['id']) ?>"
                               class="btn btn-sm btn-outline-success"
                               data-cv-confirm
                               data-cv-title="Reactivar negocio"
                               data-cv-message="¿Desea reactivar el acceso al panel de <?= esc($n['nombre']) ?>?"
                               data-cv-confirm="Sí, reactivar"
                               data-cv-cancel="Cancelar">Reactivar</a>
                        <?php else: ?>
                            <a href="<?= base_url('admin/negocios/toggle/' . $n['id']) ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-cv-confirm
                               data-cv-title="Suspender negocio"
                               data-cv-message="¿Desea suspender el acceso al panel de <?= esc($n['nombre']) ?>? El dueño y sus empleados no podrán ingresar."
                               data-cv-confirm="Sí, suspender"
                               data-cv-cancel="Cancelar">Suspender</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
