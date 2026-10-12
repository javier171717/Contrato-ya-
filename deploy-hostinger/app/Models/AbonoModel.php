<?php namespace App\Models;

use CodeIgniter\Model;

class AbonoModel extends Model
{
    protected $table = 'abonos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'negocio_id', 'contrato_id', 'usuario_id', 'monto',
        'interes_cobrado', 'capital_abonado', 'interes_capitalizado',
        'saldo_capital_anterior', 'saldo_capital_nuevo',
        'total_adeudado_antes', 'saldo_total_nuevo',
        'dias_periodo', 'tasa_mensual',
        'fecha_periodo_desde', 'fecha_abono',
        'fecha_terminacion_anterior', 'fecha_terminacion_nueva',
        'notas',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function listarPorContrato(int $contratoId, int $negocioId): array
    {
        return $this->where('contrato_id', $contratoId)
            ->where('negocio_id', $negocioId)
            ->orderBy('fecha_abono', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
