<?php helper(['contrato', 'auth']) ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato N° <?= $contrato['numero'] ?> - <?= esc($negocio['nombre']) ?></title>
    <link href="<?= base_url('css/recibo.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="no-print toolbar">
        <button onclick="window.print()" class="btn-print">
            <span>&#128424;</span> Imprimir Recibo
        </button>
        <a href="<?= base_url('contratos/ver/' . $contrato['id']) ?>" class="btn-back">Volver</a>
        <a href="<?= base_url('contratos/crear') ?>" class="btn-new">+ Nuevo Contrato</a>
    </div>

    <div class="recibo">
        <!-- Encabezado -->
        <div class="recibo-header">
            <div class="recibo-logo">
                <?php if (!empty($negocio['logo'])): ?>
                    <img src="<?= base_url($negocio['logo']) ?>" alt="Logo" class="logo-img">
                <?php else: ?>
                    <div class="logo-placeholder">&#9752;</div>
                <?php endif; ?>
            </div>
            <div class="recibo-empresa">
                <h1 class="empresa-nombre"><?= esc(strtoupper($negocio['nombre'])) ?></h1>
                <p class="empresa-nit">NIT. <?= esc($negocio['nit']) ?></p>
                <p class="empresa-dir"><?= esc($negocio['direccion']) ?></p>
                <p class="empresa-contacto">Cel: <?= esc($negocio['telefono']) ?> - <?= esc($negocio['ciudad']) ?> - <?= esc($negocio['departamento']) ?></p>
            </div>
            <div class="recibo-meta">
                <div class="meta-numero">N° <span><?= $contrato['numero'] ?></span></div>
                <div class="meta-monto"><?= formato_moneda($contrato['monto']) ?></div>
            </div>
        </div>

        <!-- Título -->
        <h2 class="recibo-titulo">CONTRATO DE COMPRAVENTA CON PACTO DE RETROVENTA</h2>

        <!-- Fechas -->
        <div class="recibo-fechas">
            <div class="fecha-box">
                <span class="fecha-label">FECHA INICIO</span>
                <span class="fecha-valor"><?= formato_fecha_recibo($contrato['fecha_inicio']) ?></span>
            </div>
            <div class="fecha-box">
                <span class="fecha-label">FECHA TERMINACIÓN</span>
                <span class="fecha-valor"><?= formato_fecha_recibo($contrato['fecha_terminacion']) ?></span>
            </div>
        </div>

        <!-- Vendedor -->
        <div class="recibo-vendedor">
            <p>
                YO, <strong class="campo-lleno"><?= esc(strtoupper($contrato['cliente_nombre'])) ?></strong>
                C.C. <strong class="campo-lleno"><?= esc($contrato['cliente_cedula']) ?></strong>
            </p>
            <p class="declaracion">
                Hago Constar que he vendido al establecimiento de comercio
                <strong><?= esc(strtoupper($negocio['nombre'])) ?></strong>
                los objetos que se relacionan a continuación.
            </p>
        </div>

        <!-- Artículo -->
        <div class="recibo-articulo">
            <div class="articulo-label">DESCRIPCIÓN DEL ARTÍCULO</div>
            <div class="articulo-valor"><?= esc($contrato['descripcion_articulo']) ?></div>
        </div>

        <!-- Texto legal -->
        <div class="recibo-legal">
            <?php
            $textoLegal = $negocio['texto_legal'] ?? '';
            $parrafos = array_filter(explode("\n\n", $textoLegal));
            foreach ($parrafos as $parrafo):
            ?>
                <p><?= esc(trim($parrafo)) ?></p>
            <?php endforeach; ?>
        </div>

        <!-- Firmas -->
        <div class="recibo-firmas">
            <div class="firma-col">
                <div class="firma-linea"></div>
                <p class="firma-titulo">Comprador</p>
                <p class="firma-cc">C.C. <?= esc($negocio['representante_cc'] ?? '_______________') ?></p>
                <?php if (!empty($negocio['representante_nombre'])): ?>
                    <p class="firma-nombre"><?= esc($negocio['representante_nombre']) ?></p>
                <?php endif; ?>
            </div>
            <div class="firma-col">
                <div class="firma-linea"></div>
                <p class="firma-titulo">Vendedor</p>
                <p class="firma-cc">C.C. <?= esc($contrato['cliente_cedula']) ?></p>
                <p class="firma-nombre"><?= esc($contrato['cliente_nombre']) ?></p>
            </div>
        </div>

        <?php if (!empty($contrato['creador_nombre'])): ?>
        <p class="recibo-elaborado">
            Elaborado por: <strong><?= esc($contrato['creador_nombre']) ?></strong>
            (<?= etiqueta_rol_usuario($contrato['creador_rol'] ?? '') ?>)
            <?php if (!empty($contrato['created_at'])): ?>
                · <?= date('d/m/Y H:i', strtotime($contrato['created_at'])) ?>
            <?php endif; ?>
        </p>
        <?php endif; ?>

        <!-- Número inferior -->
        <div class="recibo-footer">
            <div class="footer-numero">N° <?= $contrato['numero'] ?></div>
        </div>
    </div>
</body>
</html>
