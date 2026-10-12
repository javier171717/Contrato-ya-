<?= $this->extend('layouts/main') ?>
<?php helper(['contrato', 'auth']) ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-file-earmark-text text-success"></i> Contratos</h1>
    <a href="<?= base_url('contratos/crear') ?>" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nuevo
    </a>
</div>

<?php
$etiquetasEstado = [
    'activo' => 'Activos',
    'vigente' => 'Vigentes',
    'redimido' => 'Redimidos',
    'vencido' => 'Vencidos',
    'vendido' => 'Vendidos',
    'renovado' => 'Renovados',
];
$hayFiltro = ! empty($busqueda) || ! empty($estado);
$urlLimpiar = base_url('contratos');
?>

<?php if (! empty($estado)): ?>
<div class="alert alert-light border d-flex align-items-center justify-content-between py-2 mb-3">
    <span>
        <i class="bi bi-funnel text-success"></i>
        Filtrando por: <span class="badge estado-<?= esc($estado) ?>"><?= esc($etiquetasEstado[$estado] ?? ucfirst($estado)) ?></span>
    </span>
    <a href="<?= esc($urlLimpiar) ?>" class="btn btn-sm btn-outline-secondary">Ver todos</a>
</div>
<?php endif; ?>

<form method="get" class="mb-4" id="formBuscarContratos">
    <?php if (! empty($estado)): ?>
        <input type="hidden" name="estado" value="<?= esc($estado) ?>">
    <?php endif; ?>
    <div class="cv-autocomplete-wrap">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="q" id="buscarContratos" class="form-control form-control-lg"
                   placeholder="Buscar por nombre, cédula, número o artículo..."
                   value="<?= esc($busqueda ?? '') ?>"
                   autocomplete="off" autocapitalize="off" spellcheck="false">
            <button class="btn btn-success" type="submit">Buscar</button>
            <?php if ($hayFiltro): ?>
                <a href="<?= esc($urlLimpiar) ?>" class="btn btn-outline-secondary">Limpiar</a>
            <?php endif; ?>
        </div>
        <ul class="cv-autocomplete-list d-none" id="listaSugerenciasContratos" role="listbox"></ul>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($contratos)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-search display-4"></i>
                <p class="mt-2">No se encontraron contratos.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Cliente</th>
                        <th>C.C.</th>
                        <th>Artículo</th>
                        <th>Monto</th>
                        <th>Inicio</th>
                        <th>Vence</th>
                        <th>Estado</th>
                        <th>Registró</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contratos as $c): ?>
                    <tr>
                        <td><strong><?= $c['numero'] ?></strong></td>
                        <td><?= esc($c['cliente_nombre']) ?></td>
                        <td><?= esc($c['cliente_cedula']) ?></td>
                        <td class="text-truncate" style="max-width:120px"><?= esc($c['descripcion_articulo']) ?></td>
                        <td><?= formato_moneda($c['monto']) ?></td>
                        <td><?= date('d/m/y', strtotime($c['fecha_inicio'])) ?></td>
                        <td><?= date('d/m/y', strtotime($c['fecha_terminacion'])) ?></td>
                        <td><span class="badge estado-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span></td>
                        <td class="small">
                            <?= esc($c['creador_nombre'] ?? '—') ?>
                            <?php if (!empty($c['creador_rol'])): ?>
                                <br><span class="badge <?= $c['creador_rol'] === 'admin' ? 'bg-success' : 'bg-secondary' ?>"><?= etiqueta_rol_usuario($c['creador_rol']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-nowrap">
                            <a href="<?= base_url('contratos/ver/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= base_url('contratos/imprimir/' . $c['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Imprimir">
                                <i class="bi bi-printer"></i>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('buscarContratos');
    const lista = document.getElementById('listaSugerenciasContratos');
    const form = document.getElementById('formBuscarContratos');
    const urlSugerencias = '<?= base_url('api/contratos/sugerencias') ?>';
    const urlVer = '<?= base_url('contratos/ver') ?>';

    let timeout = null;
    let activo = -1;
    let items = [];

    function cerrarLista() {
        lista.classList.add('d-none');
        lista.innerHTML = '';
        activo = -1;
        items = [];
    }

    function resaltarActivo() {
        lista.querySelectorAll('.cv-autocomplete-item').forEach(function(el, i) {
            el.classList.toggle('active', i === activo);
        });
    }

    function seleccionar(item) {
        window.location.href = urlVer + '/' + item.id;
    }

    function renderLista(data) {
        if (!data.length) {
            cerrarLista();
            return;
        }

        items = data;
        activo = -1;
        lista.innerHTML = '';

        data.forEach(function(item, index) {
            const li = document.createElement('li');
            li.className = 'cv-autocomplete-item';
            li.setAttribute('role', 'option');
            li.innerHTML =
                '<span class="cv-autocomplete-main">' + escapeHtml(item.texto) + '</span>' +
                '<span class="cv-autocomplete-sub">C.C. ' + escapeHtml(item.cedula) + ' · ' + escapeHtml(item.estado) + '</span>';

            li.addEventListener('mousedown', function(e) {
                e.preventDefault();
                seleccionar(item);
            });

            li.addEventListener('mouseenter', function() {
                activo = index;
                resaltarActivo();
            });

            lista.appendChild(li);
        });

        lista.classList.remove('d-none');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function buscarSugerencias() {
        const q = input.value.trim();
        if (q.length < 2) {
            cerrarLista();
            return;
        }

        try {
            const res = await fetch(urlSugerencias + '?q=' + encodeURIComponent(q));
            const data = await res.json();
            renderLista(Array.isArray(data) ? data : []);
        } catch (e) {
            cerrarLista();
        }
    }

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(buscarSugerencias, 280);
    });

    input.addEventListener('keydown', function(e) {
        if (lista.classList.contains('d-none') || !items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activo = Math.min(activo + 1, items.length - 1);
            resaltarActivo();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activo = Math.max(activo - 1, 0);
            resaltarActivo();
        } else if (e.key === 'Enter' && activo >= 0) {
            e.preventDefault();
            seleccionar(items[activo]);
        } else if (e.key === 'Escape') {
            cerrarLista();
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.cv-autocomplete-wrap')) {
            cerrarLista();
        }
    });

    form.addEventListener('submit', function() {
        cerrarLista();
    });
});
</script>
<?= $this->endSection() ?>
