<?php namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['negocio_id', 'nombre', 'cedula', 'telefono', 'direccion'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function buscarPorCedula(int $negocioId, string $cedula): ?array
    {
        return $this->where('negocio_id', $negocioId)
            ->where('cedula', $cedula)
            ->first();
    }
}
