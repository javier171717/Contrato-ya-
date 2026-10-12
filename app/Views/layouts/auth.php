<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1a5f2a">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <?= view('partials/favicon') ?>
    <?php $sistema = config('Sistema'); ?>
    <title>Iniciar Sesión - <?= esc($sistema->nombre) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/cv-dialog.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/site-footer-marca.css') ?>" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="text-center mb-4">
                <div class="login-logo login-logo--img">
                    <img src="<?= base_url('icons/icon-192.png?v=2') ?>" alt="<?= esc($sistema->nombre) ?>" width="72" height="72">
                </div>
                <h1 class="h4 fw-bold text-success"><?= esc($sistema->nombre) ?></h1>
                <p class="text-muted small"><?= esc($sistema->descripcion) ?></p>
            </div>

            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <?= view('partials/site_footer_marca', ['footerClass' => 'site-footer-marca--light']) ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('js/cv-dialog.js') ?>"></script>
    <?= view('partials/cv_messages') ?>
    <script>window.CV_APP_BASE = <?= json_encode(rtrim(base_url(), '/') . '/') ?>;</script>
    <script src="<?= base_url('js/pwa.js') ?>"></script>
</body>
</html>
