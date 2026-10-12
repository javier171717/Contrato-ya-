<?php namespace App\Models;

use CodeIgniter\Model;

class GastoModel extends Model
{
    protected $table = 'gastos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['negocio_id', 'usuario_id', 'fecha', 'concepto', 'monto'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function listarConUsuario(int $negocioId, ?string $desde = null, ?string $hasta = null, int $limite = 200): array
    {
        $builder = $this->select('gastos.*, usuarios.nombre as usuario_nombre')
            ->join('usuarios', 'usuarios.id = gastos.usuario_id', 'left')
            ->where('gastos.negocio_id', $negocioId)
            ->orderBy('gastos.fecha', 'DESC')
            ->orderBy('gastos.id', 'DESC');

        if ($desde) {
            $builder->where('gastos.fecha >=', $desde);
        }
        if ($hasta) {
            $builder->where('gastos.fecha <=', $hasta);
        }

        return $builder->findAll($limite);
    }

    public function sumaPorRango(int $negocioId, string $desde, string $hasta): int
    {
        $row = $this->selectSum('monto', 'total')
            ->where('negocio_id', $negocioId)
            ->where('fecha >=', $desde)
            ->where('fecha <=', $hasta)
            ->first();

        return (int) round((float) ($row['total'] ?? 0));
    }
}
