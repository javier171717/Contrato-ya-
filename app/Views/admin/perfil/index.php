<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1 class="admin-title mb-4">Mi cuenta</h1>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <h2 class="h5 fw-bold mb-2">Cambiar usuario de inicio de sesión</h2>
            <p class="text-muted small mb-3">Usuario actual: <strong><?= esc($admin['username'] ?? '—') ?></strong></p>
            <form method="post" action="<?= base_url('admin/cuenta/usuario') ?>">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Usuario (inicio de sesión)</label>
                    <input type="text" name="username" class="form-control" value="<?= esc($admin['username'] ?? '') ?>" required minlength="3" pattern="[a-z0-9._-]+" autocomplete="username">
                    <div class="form-text">Minúsculas, números, punto, guión y guión bajo. Es el usuario de inicio de sesión.</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin-gradient">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="admin-card h-100">
            <h2 class="h5 fw-bold mb-3">Cambiar contraseña</h2>
            <form method="post" action="<?= base_url('admin/cuenta/password') ?>">
                <div class="mb-3">
                    <label class="form-label">Contraseña actual</label>
                    <input type="password" name="password_actual" class="form-control" required autocomplete="current-password">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nueva contraseña</label>
                    <input type="password" name="password_nueva" class="form-control" required minlength="6" autocomplete="new-password">
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmar" class="form-control" required minlength="6" autocomplete="new-password">
                </div>
                <button type="submit" class="btn-admin-gradient">Guardar contraseña</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
