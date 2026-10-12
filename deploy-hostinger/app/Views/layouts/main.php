<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1a5f2a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <?= view('partials/favicon') ?>
    <?php $sistema = config('Sistema'); ?>
    <title><?= esc($titulo ?? $sistema->nombreCorto) ?> - <?= esc(session()->get('negocio_nombre') ?? 'Sistema') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/cv-dialog.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/site-footer-marca.css') ?>" rel="stylesheet">
</head>
<body class="app-body">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?= base_url('dashboard') ?>">
                <i class="bi bi-shop-window"></i>
                <?= esc(session()->get('negocio_nombre')) ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                            <i class="bi bi-speedometer2"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'contratos') === 0 ? 'active' : '' ?>" href="<?= base_url('contratos') ?>">
                            <i class="bi bi-file-earmark-text"></i> Contratos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-new-contract" href="<?= base_url('contratos/crear') ?>">
                            <i class="bi bi-plus-circle"></i> Nuevo
                        </a>
                    </li>
                    <?php if (es_dueño()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'finanzas') === 0 ? 'active' : '' ?>" href="<?= base_url('finanzas') ?>">
                            <i class="bi bi-cash-coin"></i> Finanzas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'usuarios') === 0 ? 'active' : '' ?>" href="<?= base_url('usuarios') ?>">
                            <i class="bi bi-people"></i> Empleados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('negocio/configuracion') ?>">
                            <i class="bi bi-gear"></i> Configuración
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <span class="navbar-text text-white-50 me-3 d-none d-md-inline">
                    <?= esc(session()->get('usuario_nombre')) ?>
                    <?php if (es_empleado()): ?>
                        <span class="badge bg-light text-success ms-1">Empleado</span>
                    <?php endif; ?>
                </span>
                <a href="<?= base_url('cuenta') ?>" class="btn btn-outline-light btn-sm me-2" title="Mi cuenta">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-md-inline">Cuenta</span>
                </a>
                <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-3 py-md-4 px-3 px-md-4">
        <?= $this->renderSection('content') ?>
    </main>

    <a href="<?= base_url('contratos/crear') ?>" class="fab d-lg-none" title="Nuevo contrato">
        <i class="bi bi-plus-lg"></i>
    </a>

    <?= view('partials/site_footer_marca') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('js/cv-dialog.js') ?>"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
    <script>window.CV_APP_BASE = <?= json_encode(rtrim(base_url(), '/') . '/') ?>;</script>
    <script src="<?= base_url('js/pwa.js') ?>"></script>
    <?= view('partials/cv_messages') ?>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
