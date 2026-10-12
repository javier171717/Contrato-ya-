<?php namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $allowedFields = ['negocio_id', 'nombre', 'username', 'email', 'password', 'rol', 'activo'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function verificarLogin(string $username, string $password): ?array
    {
        $username = strtolower(trim($username));
        $usuario = $this->where('username', $username)->where('activo', 1)->first();
        if (!$usuario || !password_verify($password, $usuario['password'])) {
            return null;
        }
        return $usuario;
    }

    public function adminDelNegocio(int $negocioId): ?array
    {
        return $this->where('negocio_id', $negocioId)->where('rol', 'admin')->first();
    }

    public function listarEmpleados(int $negocioId): array
    {
        return $this->where('negocio_id', $negocioId)
            ->where('rol', 'empleado')
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }

    public function perteneceAlNegocio(int $usuarioId, int $negocioId): bool
    {
        return $this->where('id', $usuarioId)->where('negocio_id', $negocioId)->first() !== null;
    }

    public function usernameDisponible(string $username, ?int $exceptId = null): bool
    {
        $builder = $this->where('username', strtolower(trim($username)));
        if ($exceptId) {
            $builder->where('id !=', $exceptId);
        }
        return $builder->first() === null;
    }
}
