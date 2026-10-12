<?php namespace App\Libraries;

use App\Models\AbonoModel;
use App\Models\ContratoModel;
use App\Models\GastoModel;

class FinanzasService
{
    protected $abonoModel;
    protected $gastoModel;
    protected $contratoModel;

    public function __construct()
    {
        $this->abonoModel = new AbonoModel();
        $this->gastoModel = new GastoModel();
        $this->contratoModel = new ContratoModel();
        helper('contrato');
    }

    public function resumenPeriodo(int $negocioId, array $negocio, string $desde, string $hasta): array
    {
        $cobros = $this->sumaAbonos($negocioId, $desde, $hasta);
        $gastos = $this->gastoModel->sumaPorRango($negocioId, $desde, $hasta);
        $empeños = $this->sumaEmpeños($negocioId, $desde, $hasta);
        $empeñosCount = $this->cuentaEmpeños($negocioId, $desde, $hasta);

        return [
            'ingresos_efectivo' => $cobros,
            'cobros' => $cobros,
            'transferencias' => 0,
            'gastos' => $gastos,
            'empeños' => $empeños,
            'empeños_count' => $empeñosCount,
            'balance' => $cobros - $gastos - $empeños,
            'cartera' => $this->carteraPorCobrar($negocioId, $negocio),
        ];
    }

    public function resumenHoy(int $negocioId, array $negocio): array
    {
        $hoy = date('Y-m-d');
        return $this->resumenPeriodo($negocioId, $negocio, $hoy, $hoy);
    }

    public function resumenMes(int $negocioId, array $negocio): array
    {
        $desde = date('Y-m-01');
        $hasta = date('Y-m-d');
        return $this->resumenPeriodo($negocioId, $negocio, $desde, $hasta);
    }

    protected function sumaAbonos(int $negocioId, string $desde, string $hasta): int
    {
        $row = $this->abonoModel->selectSum('monto', 'total')
            ->where('negocio_id', $negocioId)
            ->where('fecha_abono >=', $desde)
            ->where('fecha_abono <=', $hasta)
            ->first();

        return (int) round((float) ($row['total'] ?? 0));
    }

    protected function sumaEmpeños(int $negocioId, string $desde, string $hasta): int
    {
        $row = $this->contratoModel->selectSum('monto', 'total')
            ->where('negocio_id', $negocioId)
            ->where('created_at >=', $desde . ' 00:00:00')
            ->where('created_at <=', $hasta . ' 23:59:59')
            ->first();

        return (int) round((float) ($row['total'] ?? 0));
    }

    protected function cuentaEmpeños(int $negocioId, string $desde, string $hasta): int
    {
        return (int) $this->contratoModel
            ->where('negocio_id', $negocioId)
            ->where('created_at >=', $desde . ' 00:00:00')
            ->where('created_at <=', $hasta . ' 23:59:59')
            ->countAllResults();
    }

    public function carteraPorCobrar(int $negocioId, array $negocio): int
    {
        $contratos = $this->contratoModel
            ->where('negocio_id', $negocioId)
            ->whereIn('estado', ['vigente', 'renovado', 'vencido'])
            ->findAll();

        $total = 0;
        foreach ($contratos as $contrato) {
            $resumen = resumen_financiero_contrato($contrato, $negocio);
            $total += $resumen['total_adeudado'];
        }

        return $total;
    }
}
