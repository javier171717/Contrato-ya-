<?php namespace App\Models;

use CodeIgniter\Model;

class PlatformAdminModel extends Model
{
    protected $table = 'platform_admins';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre', 'username', 'email', 'password', 'activo'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function verificarLogin(string $username, string $password): ?array
    {
        $username = strtolower(trim($username));
        $admin = $this->where('username', $username)->where('activo', 1)->first();
        if (!$admin || !password_verify($password, $admin['password'])) {
            return null;
        }
        return $admin;
    }

    public function usernameDisponible(string $username, ?int $exceptId = null): bool
    {
        $builder = $this->where('username', $username);
        if ($exceptId) {
            $builder->where('id !=', $exceptId);
        }
        return $builder->first() === null;
    }
}
