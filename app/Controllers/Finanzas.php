<?php namespace App\Controllers;

use App\Libraries\FinanzasService;
use App\Models\GastoModel;
use App\Models\NegocioModel;

class Finanzas extends BaseController
{
    protected $finanzas;
    protected $gastoModel;
    protected $negocioModel;

    public function __construct()
    {
        $this->finanzas = new FinanzasService();
        $this->gastoModel = new GastoModel();
        $this->negocioModel = new NegocioModel();
        helper(['contrato', 'auth']);
    }

    protected function negocioActual(): array
    {
        return $this->negocioModel->find((int) session()->get('negocio_id'));
    }

    protected function rangoFechas(string $desdeDefault, string $hastaDefault): array
    {
        $desde = $this->request->getGet('desde');
        $hasta = $this->request->getGet('hasta');

        if (!$desde || !$hasta) {
            $desde = $desdeDefault;
            $hasta = $hastaDefault;
        }

        if (strtotime($hasta) < strtotime($desde)) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        $etiqueta = $desde === $hasta
            ? formato_fecha_largo($desde)
            : formato_fecha_largo($desde) . ' — ' . formato_fecha_largo($hasta);

        return [
            'desde' => $desde,
            'hasta' => $hasta,
            'etiqueta' => $etiqueta,
        ];
    }

    public function index()
    {
        $negocio = $this->negocioActual();
        $negocioId = (int) $negocio['id'];
        $rango = $this->rangoFechas(date('Y-m-01'), date('Y-m-d'));

        return view('finanzas/index', [
            'titulo' => 'Finanzas',
            'seccion' => 'finanzas',
            'desde' => $rango['desde'],
            'hasta' => $rango['hasta'],
            'etiqueta_periodo' => $rango['etiqueta'],
            'periodo' => $this->finanzas->resumenPeriodo($negocioId, $negocio, $rango['desde'], $rango['hasta']),
            'hoy' => $this->finanzas->resumenHoy($negocioId, $negocio),
            'mes' => $this->finanzas->resumenMes($negocioId, $negocio),
        ]);
    }

    public function gastos()
    {
        $negocioId = (int) session()->get('negocio_id');
        $rango = $this->rangoFechas(date('Y-m-01'), date('Y-m-d'));

        return view('finanzas/gastos', [
            'titulo' => 'Gastos',
            'seccion' => 'gastos',
            'desde' => $rango['desde'],
            'hasta' => $rango['hasta'],
            'etiqueta_periodo' => $rango['etiqueta'],
            'gastos' => $this->gastoModel->listarConUsuario($negocioId, $rango['desde'], $rango['hasta']),
        ]);
    }

    public function guardarGasto()
    {
        $negocioId = (int) session()->get('negocio_id');

        $rules = [
            'fecha' => 'required|valid_date[Y-m-d]',
            'concepto' => 'required|min_length[2]|max_length[255]',
            'monto' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $monto = parse_monto_colombiano($this->request->getPost('monto'));
        if ($monto === null || $monto <= 0) {
            return redirect()->back()->withInput()->with('error', 'Ingrese un monto válido.');
        }

        $this->gastoModel->insert([
            'negocio_id' => $negocioId,
            'usuario_id' => session()->get('usuario_id'),
            'fecha' => $this->request->getPost('fecha'),
            'concepto' => trim($this->request->getPost('concepto')),
            'monto' => $monto,
        ]);

        return redirect()->to(site_url('finanzas/gastos'))->with('success', 'Gasto registrado correctamente.');
    }

    public function cierre()
    {
        $negocio = $this->negocioActual();
        $negocioId = (int) $negocio['id'];
        $hoy = date('Y-m-d');
        $rango = $this->rangoFechas($hoy, $hoy);

        return view('finanzas/cierre', [
            'titulo' => 'Cierre de caja',
            'seccion' => 'cierre',
            'desde' => $rango['desde'],
            'hasta' => $rango['hasta'],
            'etiqueta_periodo' => $rango['etiqueta'],
            'periodo' => $this->finanzas->resumenPeriodo($negocioId, $negocio, $rango['desde'], $rango['hasta']),
        ]);
    }
}
