<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h1 class="h3 mb-4"><i class="bi bi-person-plus"></i> Nuevo empleado</h1>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="post" action="<?= base_url('usuarios/guardar') ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre completo</label>
                        <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre')) ?>" required minlength="2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuario (inicio de sesión)</label>
                        <input type="text" name="username" class="form-control" value="<?= esc(old('username')) ?>" required minlength="3" pattern="[a-z0-9._-]+" autocomplete="off">
                        <div class="form-text">Minúsculas, números, punto, guión y guión bajo.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña inicial</label>
                        <input type="password" name="password" class="form-control" required minlength="6" autocomplete="new-password">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Correo (opcional)</label>
                        <input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" placeholder="Solo para contacto">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Crear empleado
                        </button>
                        <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
