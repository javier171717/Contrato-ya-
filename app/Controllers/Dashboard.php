<?php namespace App\Controllers;

use App\Models\ContratoModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $negocioId = session()->get('negocio_id');
        $contratoModel = new ContratoModel();

        $data = [
            'titulo' => 'Panel de Control',
            'stats' => $contratoModel->contarPorEstado($negocioId),
            'proximos' => $contratoModel->proximosVencer($negocioId, 7),
            'recientes' => $contratoModel->listarConCliente($negocioId, null, 10),
        ];

        return view('dashboard/index', $data);
    }
}
