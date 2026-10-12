<?php namespace App\Models;

use CodeIgniter\Model;

class NegocioModel extends Model
{
    protected $table = 'negocios';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre', 'slug', 'nit', 'direccion', 'telefono', 'ciudad', 'departamento',
        'logo', 'representante_nombre', 'representante_cc', 'dias_plazo_default',
        'tasa_interes_mensual', 'ultimo_numero', 'texto_legal', 'activo',
        'estado_plan', 'plan_codigo', 'fin_prueba', 'proximo_cobro', 'suspendido_admin',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function siguienteNumero(int $negocioId): int
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $negocio = $this->find($negocioId);
        if (!$negocio) {
            throw new \RuntimeException('Negocio no encontrado');
        }

        $siguiente = (int) $negocio['ultimo_numero'] + 1;
        $this->update($negocioId, ['ultimo_numero' => $siguiente]);

        $db->transComplete();
        return $siguiente;
    }

    public function listarConAdmin(): array
    {
        return $this->select('negocios.*, usuarios.nombre as admin_nombre, usuarios.email as admin_email, usuarios.username as admin_username')
            ->join('usuarios', 'usuarios.negocio_id = negocios.id AND usuarios.rol = \'admin\'', 'left')
            ->orderBy('negocios.nombre', 'ASC')
            ->findAll();
    }

    public function obtenerConAdmin(int $id): ?array
    {
        return $this->select('negocios.*, usuarios.id as admin_id, usuarios.nombre as admin_nombre, usuarios.email as admin_email, usuarios.username as admin_username')
            ->join('usuarios', 'usuarios.negocio_id = negocios.id AND usuarios.rol = \'admin\'', 'left')
            ->where('negocios.id', $id)
            ->first();
    }

    public function generarSlug(string $nombre): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $nombre)), '-'));
        if ($slug === '') {
            $slug = 'negocio';
        }
        $base = $slug;
        $i = 1;
        while ($this->where('slug', $slug)->first()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function puedeAccederPanel(?array $negocio): bool
    {
        if (!$negocio || !(int) ($negocio['activo'] ?? 0)) {
            return false;
        }
        if ((int) ($negocio['suspendido_admin'] ?? 0) === 1) {
            return false;
        }
        if (($negocio['estado_plan'] ?? '') === 'suspendido_mora') {
            return false;
        }
        if (($negocio['estado_plan'] ?? '') === 'prueba') {
            if (empty($negocio['fin_prueba'])) {
                return false;
            }
            return strtotime($negocio['fin_prueba']) >= time();
        }
        return true;
    }

    public function etiquetaPlan(?array $negocio): string
    {
        if (!$negocio) {
            return '—';
        }
        if ((int) ($negocio['suspendido_admin'] ?? 0) === 1) {
            return 'Suspendido (admin)';
        }
        switch ($negocio['estado_plan'] ?? 'prueba') {
            case 'activo': return 'Activo (pagando)';
            case 'suspendido_mora': return 'Suspendido por mora';
            default: return 'En prueba';
        }
    }

    public function etiquetaPanel(?array $negocio): string
    {
        return $this->puedeAccederPanel($negocio) ? 'Activo' : 'Suspendido';
    }
}
