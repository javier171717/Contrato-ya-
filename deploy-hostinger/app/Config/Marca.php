<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Marca extends BaseConfig
{
    public string $webUrl = 'https://sanabriacod.com';
    public string $waUrl = 'https://wa.me/573001234567';

    public function __construct()
    {
        parent::__construct();

        $this->webUrl = env('marca.webUrl', $this->webUrl);
        $this->waUrl = env('marca.waUrl', $this->waUrl);
    }
}
