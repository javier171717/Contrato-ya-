<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<form action="<?= base_url('login') ?>" method="post" autocomplete="off">
    <div class="mb-3">
        <label for="username" class="form-label">Usuario</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control form-control-lg" id="username" name="username"
                   value="<?= esc(old('username') ?? '') ?>" required autofocus
                   placeholder="" autocomplete="off" autocapitalize="none" spellcheck="false">
        </div>
    </div>
    <div class="mb-4">
        <label for="password" class="form-label">Contraseña</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control form-control-lg" id="password" name="password"
                   required placeholder="" autocomplete="off">
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-lg w-100">
        <i class="bi bi-box-arrow-in-right"></i> Ingresar
    </button>
</form>
<?= $this->endSection() ?>
