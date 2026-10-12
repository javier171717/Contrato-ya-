<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="bi bi-plus-circle text-success"></i> Nuevo Contrato</h1>
            <span class="badge bg-success fs-6">N° <?= $siguiente_numero ?></span>
        </div>

        <form action="<?= base_url('contratos/guardar') ?>" method="post" id="formContrato">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-person"></i> Datos del Vendedor
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="cedula" class="form-label">C.C. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="cedula" name="cedula"
                                   value="<?= old('cedula') ?>" required inputmode="numeric"
                                   autocomplete="off">
                            <div class="form-text">Al digitar busca cliente existente</div>
                        </div>
                        <div class="col-md-8">
                            <label for="nombre" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="nombre" name="nombre"
                                   value="<?= old('nombre') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono"
                                   value="<?= old('telefono') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-box"></i> Artículo y Valor
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="descripcion_articulo" class="form-label">Descripción del artículo <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-lg" id="descripcion_articulo" name="descripcion_articulo"
                                  rows="2" required><?= old('descripcion_articulo') ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="monto" class="form-label">Valor ($) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control" id="monto" name="monto"
                                       value="<?= esc(formato_monto_input(old('monto'))) ?>" required
                                       inputmode="numeric" autocomplete="off">
                            </div>
                            <div class="form-text">Use punto para miles. Ej: 400.000 = cuatrocientos mil pesos.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-calendar"></i> Fechas del Contrato
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="fecha_inicio" class="form-label">Fecha inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg" id="fecha_inicio" name="fecha_inicio"
                                   value="<?= old('fecha_inicio', date('Y-m-d')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_terminacion" class="form-label">Fecha terminación <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg" id="fecha_terminacion" name="fecha_terminacion"
                                   value="<?= old('fecha_terminacion', date('Y-m-d', strtotime('+' . $negocio['dias_plazo_default'] . ' days'))) ?>" required>
                            <div class="form-text">Plazo por defecto: <?= $negocio['dias_plazo_default'] ?> días</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?= base_url('contratos') ?>" class="btn btn-outline-secondary btn-lg">Cancelar</a>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle"></i> Guardar e Imprimir
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cedulaInput = document.getElementById('cedula');
    const nombreInput = document.getElementById('nombre');
    const telefonoInput = document.getElementById('telefono');
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaTerminacion = document.getElementById('fecha_terminacion');
    const montoInput = document.getElementById('monto');
    const diasPlazo = <?= (int) $negocio['dias_plazo_default'] ?>;

    montoInput.addEventListener('input', function() {
        const digits = this.value.replace(/\D/g, '');
        if (!digits) {
            this.value = '';
            return;
        }
        this.value = parseInt(digits, 10).toLocaleString('es-CO');
    });

    let timeout;
    cedulaInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const cedula = this.value.trim();
        if (cedula.length < 5) return;

        timeout = setTimeout(async () => {
            try {
                const res = await fetch('<?= base_url('api/cliente') ?>?cedula=' + encodeURIComponent(cedula));
                const data = await res.json();
                if (data.found) {
                    nombreInput.value = data.nombre;
                    if (data.telefono) telefonoInput.value = data.telefono;
                    nombreInput.classList.add('is-valid');
                } else {
                    nombreInput.classList.remove('is-valid');
                }
            } catch (e) {}
        }, 400);
    });

    fechaInicio.addEventListener('change', function() {
        if (!this.value) return;
        const inicio = new Date(this.value + 'T00:00:00');
        inicio.setDate(inicio.getDate() + diasPlazo);
        fechaTerminacion.value = inicio.toISOString().split('T')[0];
    });
});
</script>
<?= $this->endSection() ?>
