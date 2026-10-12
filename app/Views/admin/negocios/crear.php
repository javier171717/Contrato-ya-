<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('admin/negocios') ?>" class="text-muted small"><i class="bi bi-arrow-left"></i> Volver al listado</a>
    <h1 class="admin-title mt-2">Alta de negocio y dueño</h1>
    <p class="admin-subtitle">El negocio nuevo recibe <strong>14 días de prueba</strong> automáticamente.</p>
</div>

<form method="post" action="<?= base_url('admin/negocios/guardar') ?>">
    <div class="admin-card mb-3">
        <h2 class="h6 fw-bold mb-3">Datos del negocio</h2>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Nombre del negocio *</label>
                <input type="text" name="nombre" class="form-control" value="<?= old('nombre') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">NIT *</label>
                <input type="text" name="nit" class="form-control" value="<?= old('nit') ?>" required placeholder="900.123.456-7">
            </div>
            <div class="col-12">
                <label class="form-label">Dirección *</label>
                <input type="text" name="direccion" class="form-control" value="<?= old('direccion') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Teléfono *</label>
                <input type="text" name="telefono" class="form-control" value="<?= old('telefono') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Ciudad *</label>
                <input type="text" name="ciudad" class="form-control" value="<?= old('ciudad') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Departamento *</label>
                <input type="text" name="departamento" class="form-control" value="<?= old('departamento', 'Antioquia') ?>" required>
            </div>
        </div>
    </div>

    <div class="admin-card mb-4">
        <h2 class="h6 fw-bold mb-3">Administrador del negocio (dueño)</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre completo *</label>
                <input type="text" name="admin_nombre" class="form-control" value="<?= old('admin_nombre') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Usuario de inicio de sesión *</label>
                <input type="text" name="admin_username" class="form-control" value="<?= old('admin_username') ?>" required minlength="3" pattern="[a-z0-9._-]+" placeholder="">
                <div class="form-text">Minúsculas, números, punto, guión y guión bajo.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo electrónico *</label>
                <input type="email" name="admin_email" class="form-control" value="<?= old('admin_email') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contraseña inicial *</label>
                <input type="password" name="admin_password" class="form-control" required minlength="6">
            </div>
        </div>
    </div>

    <button type="submit" class="btn-admin-gradient btn-lg">Crear negocio</button>
</form>
<?= $this->endSection() ?>
