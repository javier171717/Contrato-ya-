<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <h1 class="h3 mb-4"><i class="bi bi-key"></i> Cambiar contraseña</h1>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <p class="mb-4">
                    Empleado: <strong><?= esc($empleado['nombre']) ?></strong><br>
                    Usuario: <code><?= esc($empleado['username']) ?></code>
                </p>

                <form method="post" action="<?= base_url('usuarios/password/' . $empleado['id']) ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva contraseña</label>
                        <input type="password" name="password" class="form-control" required minlength="6" autocomplete="new-password">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-shield-lock"></i> Guardar
                        </button>
                        <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
