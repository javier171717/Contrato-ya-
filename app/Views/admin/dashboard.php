<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $nm = new \App\Models\NegocioModel(); ?>

<div class="admin-page-header">
    <h1 class="admin-title">Administración de plataforma</h1>
    <p class="admin-subtitle">Gestione negocios y accesos de alto nivel. El día a día de contratos está en el panel de cada negocio.</p>
</div>

<div class="admin-card admin-card-action">
    <a href="<?= base_url('admin/negocios') ?>" class="btn-admin-gradient btn-lg">
        <i class="bi bi-building"></i> Listado de negocios
    </a>
</div>
<?= $this->endSection() ?>
