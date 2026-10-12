<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-people"></i> Empleados</h1>
    <a href="<?= base_url('usuarios/crear') ?>" class="btn btn-success">
        <i class="bi bi-person-plus"></i> Nuevo empleado
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <?php if (empty($empleados)): ?>
            <div class="p-4 text-center text-muted">
                <i class="bi bi-person-x display-4 d-block mb-2"></i>
                No hay empleados registrados. Cree perfiles para que operen contratos sin acceso a la configuración.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empleados as $e): ?>
                        <tr>
                            <td><?= esc($e['nombre']) ?></td>
                            <td><code><?= esc($e['username']) ?></code></td>
                            <td>
                                <?php if ((int) $e['activo'] === 1): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('usuarios/password/' . $e['id']) ?>" class="btn btn-sm btn-outline-primary" title="Cambiar contraseña">
                                    <i class="bi bi-key"></i>
                                </a>
                                <a href="<?= base_url('usuarios/toggle/' . $e['id']) ?>"
                                   class="btn btn-sm btn-outline-secondary"
                                   data-cv-confirm
                                   data-cv-title="<?= (int) $e['activo'] === 1 ? 'Desactivar empleado' : 'Activar empleado' ?>"
                                   data-cv-message="<?= (int) $e['activo'] === 1
                                       ? '¿Desea desactivar el acceso de ' . esc($e['nombre']) . '? No podrá iniciar sesión hasta que lo reactive.'
                                       : '¿Desea activar nuevamente a ' . esc($e['nombre']) . '? Podrá volver a ingresar al sistema.' ?>"
                                   data-cv-confirm="<?= (int) $e['activo'] === 1 ? 'Sí, desactivar' : 'Sí, activar' ?>"
                                   data-cv-cancel="Cancelar">
                                    <i class="bi bi-<?= (int) $e['activo'] === 1 ? 'pause' : 'play' ?>-circle"></i>
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

<p class="text-muted small mt-3 mb-0">
    Los empleados pueden ver el panel, crear contratos e imprimir recibos. No tienen acceso a configuración ni a esta sección.
</p>
<?= $this->endSection() ?>
