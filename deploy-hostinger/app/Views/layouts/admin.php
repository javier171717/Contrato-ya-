<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1a5f2a">
    <?= view('partials/favicon') ?>
    <?php $sistema = config('Sistema'); ?>
    <title><?= esc($titulo ?? 'Plataforma') ?> - <?= esc($sistema->nombre) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/admin.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/cv-dialog.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/site-footer-marca.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
    <header class="admin-header">
        <div class="container-fluid px-3 px-md-4">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div>
                    <a href="<?= base_url('admin') ?>" class="admin-brand">
                        <i class="bi bi-shop-window"></i> <?= esc($sistema->nombre) ?>
                    </a>
                    <div class="admin-user-line">
                        <?= esc(session()->get('platform_admin_nombre')) ?>
                        <?php if (session()->get('platform_admin_username')): ?>
                            · usuario <?= esc(session()->get('platform_admin_username')) ?>
                        <?php endif; ?>
                        · Administrador de plataforma
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('admin/cuenta') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-person-gear"></i> Mi cuenta
                    </a>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-secondary btn-sm">Salir</a>
                </div>
            </div>
            <nav class="admin-nav">
                <a href="<?= base_url('admin/cuenta') ?>" class="admin-nav-link <?= strpos(uri_string(), 'admin/cuenta') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-person-gear"></i> Mi cuenta
                </a>
                <a href="<?= base_url('admin/negocios') ?>" class="admin-nav-link <?= strpos(uri_string(), 'admin/negocios') === 0 ? 'active' : '' ?>">
                    <i class="bi bi-building"></i> Negocios
                </a>
            </nav>
        </div>
    </header>

    <div class="admin-welcome">
        <div class="container-fluid px-3 px-md-4">
            Bienvenido, <?= esc(session()->get('platform_admin_nombre')) ?>.
        </div>
    </div>

    <main class="container-fluid px-3 px-md-4 py-4">
        <?= $this->renderSection('content') ?>
    </main>

    <?= view('partials/site_footer_marca') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('js/cv-dialog.js') ?>"></script>
    <?= view('partials/cv_messages') ?>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
