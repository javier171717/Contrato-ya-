<?= $this->extend('layouts/admin') ?>
<?php helper('contrato'); $nm = new \App\Models\NegocioModel(); ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <a href="<?= base_url('admin/negocios') ?>" class="text-muted small"><i class="bi bi-arrow-left"></i> Volver al listado</a>
</div>

<div class="admin-card mb-3">
    <div class="d-flex align-items-start gap-2">
        <h1 class="admin-title mb-1"><?= esc($negocio['nombre']) ?></h1>
        <span class="badge bg-light text-dark border">#<?= $negocio['id'] ?></span>
    </div>
    <div class="admin-meta mt-3">
        <p class="mb-1"><strong>Alta:</strong> <?= formato_datetime($negocio['created_at']) ?></p>
        <p class="mb-1"><strong>NIT:</strong> <?= esc($negocio['nit']) ?></p>
        <p class="mb-1"><strong>Plan comercial:</strong> <?= esc($nm->etiquetaPlan($negocio)) ?></p>
        <p class="mb-1">
            <strong>Fin de prueba:</strong> <?= formato_datetime($negocio['fin_prueba']) ?>
            <?php if (!empty($negocio['proximo_cobro'])): ?>
                · <strong>Próximo cobro:</strong> <?= formato_datetime($negocio['proximo_cobro']) ?>
            <?php endif; ?>
        </p>
        <p class="mb-1"><strong>Estado panel:</strong>
            <span class="badge <?= $nm->puedeAccederPanel($negocio) ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger' ?>">
                <?= esc($nm->etiquetaPanel($negocio)) ?>
            </span>
        </p>
        <p class="mb-0"><strong>Administrador del negocio:</strong>
            <?= esc($negocio['admin_nombre'] ?? '—') ?>
            <?php if (!empty($negocio['admin_username'])): ?> · usuario <?= esc($negocio['admin_username']) ?><?php endif; ?>
            <?php if (!empty($negocio['admin_email'])): ?> · <?= esc($negocio['admin_email']) ?><?php endif; ?>
        </p>
    </div>
</div>

<ul class="nav admin-tabs mb-3 flex-wrap">
    <li class="nav-item"><a class="nav-link <?= $tab === 'plan' ? 'active' : '' ?>" href="<?= base_url('admin/negocios/ver/' . $negocio['id'] . '?tab=plan') ?>">Plan y cobros</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'negocio' ? 'active' : '' ?>" href="<?= base_url('admin/negocios/ver/' . $negocio['id'] . '?tab=negocio') ?>">Editar negocio / NIT</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'admin' ? 'active' : '' ?>" href="<?= base_url('admin/negocios/ver/' . $negocio['id'] . '?tab=admin') ?>">Cambiar usuario admin</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'dueno' ? 'active' : '' ?>" href="<?= base_url('admin/negocios/ver/' . $negocio['id'] . '?tab=dueno') ?>">Datos del dueño</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'password' ? 'active' : '' ?>" href="<?= base_url('admin/negocios/ver/' . $negocio['id'] . '?tab=password') ?>">Nueva contraseña admin</a></li>
    <li class="nav-item"><a class="nav-link text-danger <?= $tab === 'eliminar' ? 'active' : '' ?>" href="<?= base_url('admin/negocios/ver/' . $negocio['id'] . '?tab=eliminar') ?>">Eliminar negocio…</a></li>
</ul>

<?php if ($tab === 'plan'): ?>
<div class="admin-card">
    <h2 class="h5 fw-bold mb-2">Plan y cobros</h2>
    <p class="text-muted small">Definí si está en prueba, pagando o con cobro vencido. <strong>Suspendido por plan / mora</strong> impide que dueños y empleados entren al panel. El botón <em>Suspender negocio</em> (abajo) es aparte: bloquea por decisión administrativa. Con plan <em>En prueba</em>, el panel solo queda habilitado si la fecha de fin de prueba no pasó.</p>

    <form method="post" action="<?= base_url('admin/negocios/plan/' . $negocio['id']) ?>" class="mt-4">
        <div class="mb-3">
            <label class="form-label fw-semibold">Estado del plan</label>
            <select name="estado_plan" class="form-select">
                <option value="prueba" <?= $negocio['estado_plan'] === 'prueba' ? 'selected' : '' ?>>En prueba</option>
                <option value="activo" <?= $negocio['estado_plan'] === 'activo' ? 'selected' : '' ?>>Activo — pagando</option>
                <option value="suspendido_mora" <?= $negocio['estado_plan'] === 'suspendido_mora' ? 'selected' : '' ?>>Suspendido por plan / mora</option>
            </select>
            <div class="form-text">Al cobrar el mes, podés poner Activo — pagando y la fecha de próximo cobro.</div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Código o etiqueta de plan (opcional, interno)</label>
            <input type="text" name="plan_codigo" class="form-control" value="<?= esc($negocio['plan_codigo'] ?? '') ?>" placeholder="ej. mensual, anual">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Fin de la prueba gratuita</label>
            <input type="datetime-local" name="fin_prueba" class="form-control" value="<?= formato_datetime_input($negocio['fin_prueba'] ?? '') ?>">
            <div class="form-text">Los negocios nuevos reciben 14 días de prueba al crearse; podés corregir o alargar acá.</div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Próximo cobro mensual (opcional)</label>
            <input type="datetime-local" name="proximo_cobro" class="form-control" value="<?= formato_datetime_input($negocio['proximo_cobro'] ?? '') ?>">
            <div class="form-text">Vacío si no hay mensualidad. Para borrar la fecha, dejá el campo vacío y guardá.</div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar plan y fechas</button>
    </form>
</div>

<?php elseif ($tab === 'negocio'): ?>
<div class="admin-card">
    <h2 class="h5 fw-bold mb-3">Editar negocio / NIT</h2>
    <form method="post" action="<?= base_url('admin/negocios/negocio/' . $negocio['id']) ?>">
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" value="<?= esc($negocio['nombre']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">NIT</label><input type="text" name="nit" class="form-control" value="<?= esc($negocio['nit']) ?>" required></div>
            <div class="col-12"><label class="form-label">Dirección</label><input type="text" name="direccion" class="form-control" value="<?= esc($negocio['direccion']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Teléfono</label><input type="text" name="telefono" class="form-control" value="<?= esc($negocio['telefono']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Ciudad</label><input type="text" name="ciudad" class="form-control" value="<?= esc($negocio['ciudad']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Departamento</label><input type="text" name="departamento" class="form-control" value="<?= esc($negocio['departamento']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Plazo contrato (días)</label><input type="number" name="dias_plazo_default" class="form-control" value="<?= (int) $negocio['dias_plazo_default'] ?>" min="1"></div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">Guardar cambios</button>
    </form>
</div>

<?php elseif ($tab === 'admin'): ?>
<div class="admin-card">
    <h2 class="h5 fw-bold mb-2">Cambiar usuario admin</h2>
    <p class="text-muted small mb-4">Negocio: <strong><?= esc($negocio['nombre']) ?></strong> · Dueño actual: <strong><?= esc($negocio['admin_nombre'] ?? '—') ?></strong></p>
    <form method="post" action="<?= base_url('admin/negocios/admin/' . $negocio['id']) ?>">
        <div class="col-md-6 mb-4">
            <label class="form-label fw-semibold">Usuario (inicio de sesión)</label>
            <input type="text" name="admin_username" class="form-control form-control-lg" value="<?= esc($negocio['admin_username'] ?? '') ?>" required minlength="3" pattern="[a-z0-9._-]+" autocomplete="off">
                    <div class="form-text">El dueño entra al panel solo con este usuario (no correo).</div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn-admin-gradient">Guardar</button>
            <a href="<?= base_url('admin/negocios/ver/' . $negocio['id']) ?>" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php elseif ($tab === 'dueno'): ?>
<div class="admin-card">
    <h2 class="h5 fw-bold mb-3">Datos del dueño</h2>
    <form method="post" action="<?= base_url('admin/negocios/dueno/' . $negocio['id']) ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre completo</label>
                <input type="text" name="admin_nombre" class="form-control" value="<?= esc($negocio['admin_nombre'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="admin_email" class="form-control" value="<?= esc($negocio['admin_email'] ?? '') ?>" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">Guardar datos del dueño</button>
    </form>
</div>

<?php elseif ($tab === 'password'): ?>
<div class="admin-card">
    <h2 class="h5 fw-bold mb-3">Nueva contraseña admin</h2>
    <p class="text-muted">Usuario: <strong><?= esc($negocio['admin_username'] ?? '—') ?></strong>
        <?php if (!empty($negocio['admin_email'])): ?> · <?= esc($negocio['admin_email']) ?><?php endif; ?></p>
    <form method="post" action="<?= base_url('admin/negocios/password/' . $negocio['id']) ?>">
        <div class="col-md-6 mb-3">
            <label class="form-label">Nueva contraseña</label>
            <input type="password" name="admin_password" class="form-control" required minlength="6" autocomplete="new-password">
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn-admin-gradient">Guardar nueva contraseña</button>
            <a href="<?= base_url('admin/negocios/ver/' . $negocio['id']) ?>" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php elseif ($tab === 'eliminar'): ?>
<div class="admin-card border-danger">
    <h2 class="h5 fw-bold text-danger mb-3">Eliminar negocio y datos</h2>
    <p class="text-muted">Se eliminarán contratos, clientes y usuarios de <strong><?= esc($negocio['nombre']) ?></strong>. Esta acción no se puede deshacer.</p>
    <form method="post" action="<?= base_url('admin/negocios/eliminar/' . $negocio['id']) ?>"
          data-cv-confirm
          data-cv-title="Eliminar negocio"
          data-cv-message="Se eliminarán contratos, clientes y usuarios de <?= esc($negocio['nombre']) ?>. Esta acción no se puede deshacer."
          data-cv-confirm="Sí, eliminar"
          data-cv-cancel="Cancelar">
        <label class="form-label">Escriba <code><?= esc($negocio['slug']) ?></code> para confirmar</label>
        <input type="text" name="confirmar" class="form-control mb-3" required placeholder="<?= esc($negocio['slug']) ?>">
        <button type="submit" class="btn btn-danger">Eliminar negocio y todos los datos</button>
    </form>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
    <a href="<?= base_url('admin/negocios') ?>" class="text-muted">← Volver al listado</a>
    <?php if ((int) $negocio['suspendido_admin'] === 1): ?>
        <a href="<?= base_url('admin/negocios/toggle/' . $negocio['id']) ?>"
           class="btn btn-outline-success"
           data-cv-confirm
           data-cv-title="Reactivar negocio"
           data-cv-message="¿Desea reactivar el acceso al panel de <?= esc($negocio['nombre']) ?>?"
           data-cv-confirm="Sí, reactivar"
           data-cv-cancel="Cancelar">Reactivar negocio</a>
    <?php else: ?>
        <a href="<?= base_url('admin/negocios/toggle/' . $negocio['id']) ?>"
           class="btn btn-outline-danger"
           data-cv-confirm
           data-cv-title="Suspender negocio"
           data-cv-message="¿Desea suspender administrativamente a <?= esc($negocio['nombre']) ?>? Nadie del negocio podrá ingresar al panel."
           data-cv-confirm="Sí, suspender"
           data-cv-cancel="Cancelar">Suspender negocio</a>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
