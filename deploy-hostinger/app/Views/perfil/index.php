<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <h1 class="h3 mb-4"><i class="bi bi-person-circle"></i> Mi cuenta</h1>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <p class="text-muted mb-4">
                    Usuario: <strong><?= esc($usuario['username']) ?></strong><br>
                    Nombre: <strong><?= esc($usuario['nombre']) ?></strong>
                </p>

                <h2 class="h5 fw-bold mb-3">Cambiar contraseña</h2>
                <form method="post" action="<?= base_url('cuenta/password') ?>">
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
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-shield-lock"></i> Guardar contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
