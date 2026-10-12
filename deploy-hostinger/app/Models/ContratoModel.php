<?php namespace App\Models;

use CodeIgniter\Model;

class ContratoModel extends Model
{
    protected $table = 'contratos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'negocio_id', 'cliente_id', 'usuario_id', 'numero', 'monto', 'saldo_capital',
        'fecha_inicio', 'fecha_terminacion', 'fecha_ultimo_movimiento',
        'descripcion_articulo', 'estado', 'observaciones',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected function joinClienteYCreador()
    {
        return $this->select(
            'contratos.*, clientes.nombre as cliente_nombre, clientes.cedula as cliente_cedula, ' .
            'usuarios.nombre as creador_nombre, usuarios.rol as creador_rol'
        )
            ->join('clientes', 'clientes.id = contratos.cliente_id')
            ->join('usuarios', 'usuarios.id = contratos.usuario_id', 'left');
    }

    public function listarConCliente(int $negocioId, ?string $busqueda = null, int $limite = 50, ?string $estado = null): array
    {
        $builder = $this->joinClienteYCreador()
            ->where('contratos.negocio_id', $negocioId)
            ->orderBy('contratos.numero', 'DESC');

        if ($estado !== null && $estado !== '') {
            if ($estado === 'activo') {
                $builder->whereIn('contratos.estado', ['vigente', 'renovado']);
            } else {
                $builder->where('contratos.estado', $estado);
            }
        }

        if ($busqueda) {
            $builder->groupStart()
                ->like('clientes.nombre', $busqueda)
                ->orLike('clientes.cedula', $busqueda)
                ->orLike('contratos.numero', $busqueda)
                ->orLike('contratos.descripcion_articulo', $busqueda)
                ->groupEnd();
        }

        return $builder->findAll($limite);
    }

    public function numeroMaximo(int $negocioId): int
    {
        $row = $this->where('negocio_id', $negocioId)->selectMax('numero', 'maximo')->first();

        return (int) ($row['maximo'] ?? 0);
    }

    public function numeroExiste(int $negocioId, int $numero): bool
    {
        return $this->where('negocio_id', $negocioId)->where('numero', $numero)->first() !== null;
    }

    public function sugerenciasBusqueda(int $negocioId, string $busqueda, int $limite = 8): array
    {
        $busqueda = trim($busqueda);
        if ($busqueda === '' || strlen($busqueda) < 2) {
            return [];
        }

        return $this->select('contratos.id, contratos.numero, contratos.descripcion_articulo, contratos.estado, clientes.nombre as cliente_nombre, clientes.cedula as cliente_cedula')
            ->join('clientes', 'clientes.id = contratos.cliente_id')
            ->where('contratos.negocio_id', $negocioId)
            ->groupStart()
                ->like('clientes.nombre', $busqueda)
                ->orLike('clientes.cedula', $busqueda)
                ->orLike('contratos.numero', $busqueda)
                ->orLike('contratos.descripcion_articulo', $busqueda)
            ->groupEnd()
            ->orderBy('contratos.numero', 'DESC')
            ->findAll($limite);
    }

    public function obtenerCompleto(int $id, int $negocioId): ?array
    {
        return $this->select(
            'contratos.*, clientes.nombre as cliente_nombre, clientes.cedula as cliente_cedula, ' .
            'clientes.telefono as cliente_telefono, usuarios.nombre as creador_nombre, usuarios.rol as creador_rol'
        )
            ->join('clientes', 'clientes.id = contratos.cliente_id')
            ->join('usuarios', 'usuarios.id = contratos.usuario_id', 'left')
            ->where('contratos.id', $id)
            ->where('contratos.negocio_id', $negocioId)
            ->first();
    }

    public function contarPorEstado(int $negocioId): array
    {
        $result = $this->select('estado, COUNT(*) as total')
            ->where('negocio_id', $negocioId)
            ->groupBy('estado')
            ->findAll();

        $stats = ['vigente' => 0, 'renovado' => 0, 'redimido' => 0, 'vencido' => 0, 'vendido' => 0];
        foreach ($result as $row) {
            $stats[$row['estado']] = (int) $row['total'];
        }
        return $stats;
    }

    public function proximosVencer(int $negocioId, int $dias = 7): array
    {
        $hoy = date('Y-m-d');
        $limite = date('Y-m-d', strtotime("+{$dias} days"));

        return $this->select('contratos.*, clientes.nombre as cliente_nombre, clientes.cedula as cliente_cedula')
            ->join('clientes', 'clientes.id = contratos.cliente_id')
            ->where('contratos.negocio_id', $negocioId)
            ->whereIn('contratos.estado', ['vigente', 'renovado'])
            ->where('contratos.fecha_terminacion >=', $hoy)
            ->where('contratos.fecha_terminacion <=', $limite)
            ->orderBy('contratos.fecha_terminacion', 'ASC')
            ->findAll();
    }
}
