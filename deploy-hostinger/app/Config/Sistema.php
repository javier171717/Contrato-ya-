<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Sistema extends BaseConfig
{
    public string $nombre = 'ContratoYa';
    public string $nombreCorto = 'ContratoYa';
    public string $descripcion = 'Sistema de contratos con pacto de retroventa';
    public string $tagline = 'Multinegocios · Funciona sin conexión';

    public function __construct()
    {
        parent::__construct();

        $this->nombre = env('sistema.nombre', $this->nombre);
        $this->nombreCorto = env('sistema.nombreCorto', $this->nombreCorto);
        $this->descripcion = env('sistema.descripcion', $this->descripcion);
        $this->tagline = env('sistema.tagline', $this->tagline);
    }
}
