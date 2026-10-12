<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <h1 class="h3 mb-4"><i class="bi bi-gear text-success"></i> Configuración del Negocio</h1>

        <form action="<?= base_url('negocio/actualizar') ?>" method="post" enctype="multipart/form-data">
            <div class="card mb-3">
                <div class="card-header">Datos del establecimiento</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre del negocio</label>
                            <input type="text" class="form-control" name="nombre"
                                   value="<?= esc($negocio['nombre']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">NIT</label>
                            <input type="text" class="form-control" name="nit"
                                   value="<?= esc($negocio['nit']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion"
                                   value="<?= esc($negocio['direccion']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Teléfono / Celular</label>
                            <input type="text" class="form-control" name="telefono"
                                   value="<?= esc($negocio['telefono']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ciudad</label>
                            <input type="text" class="form-control" name="ciudad"
                                   value="<?= esc($negocio['ciudad']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Departamento</label>
                            <input type="text" class="form-control" name="departamento"
                                   value="<?= esc($negocio['departamento']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Logo (opcional)</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            <?php if (!empty($negocio['logo'])): ?>
                                <img src="<?= base_url($negocio['logo']) ?>" alt="Logo" class="mt-2" style="max-height:60px">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Plazo por defecto (días)</label>
                            <input type="number" class="form-control" name="dias_plazo_default"
                                   value="<?= esc($negocio['dias_plazo_default']) ?>" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Interés mensual (%)</label>
                            <input type="number" class="form-control" name="tasa_interes_mensual"
                                   value="<?= esc($negocio['tasa_interes_mensual'] ?? 10) ?>" min="0" max="100" step="0.5" required>
                            <div class="form-text">Ej: 10 = 10% mensual sobre el saldo.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-hash"></i> Numeración de recibos</div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Último número usado</label>
                            <input type="number" class="form-control form-control-lg" name="ultimo_numero"
                                   id="ultimoNumero"
                                   value="<?= esc(old('ultimo_numero', $negocio['ultimo_numero'])) ?>"
                                   min="<?= (int) $numero_maximo_contrato ?>" required>
                            <div class="form-text">
                                Mínimo permitido: <strong><?= (int) $numero_maximo_contrato ?></strong>
                                <?php if ($numero_maximo_contrato > 0): ?>
                                    (recibo N° <?= (int) $numero_maximo_contrato ?> ya existe)
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Próximo recibo será</label>
                            <div class="fs-4 fw-bold text-success" id="proximoNumero">
                                N° <?= (int) $negocio['ultimo_numero'] + 1 ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-0">
                                Cada contrato recibe un número único. Puede subir este valor para continuar una numeración anterior o saltar números, pero nunca bajar del último recibo ya creado.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Representante legal (Comprador en el recibo)</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre del representante</label>
                            <input type="text" class="form-control" name="representante_nombre"
                                   value="<?= esc($negocio['representante_nombre'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">C.C. Representante</label>
                            <input type="text" class="form-control" name="representante_cc"
                                   value="<?= esc($negocio['representante_cc'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Texto legal del contrato</div>
                <div class="card-body">
                    <textarea class="form-control" name="texto_legal" rows="12"><?= esc($negocio['texto_legal'] ?? '') ?></textarea>
                    <div class="form-text">Separe párrafos con una línea en blanco. Este texto aparece en todos los recibos.</div>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-lg">
                <i class="bi bi-check-circle"></i> Guardar Configuración
            </button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('ultimoNumero');
    const preview = document.getElementById('proximoNumero');
    if (!input || !preview) return;

    input.addEventListener('input', function() {
        const n = parseInt(this.value, 10);
        preview.textContent = 'N° ' + (isNaN(n) ? '—' : n + 1);
    });
});
</script>
<?= $this->endSection() ?>
