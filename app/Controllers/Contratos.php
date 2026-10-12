<?php namespace App\Controllers;

use App\Models\ContratoModel;
use App\Models\ClienteModel;
use App\Models\NegocioModel;
use App\Models\AbonoModel;

class Contratos extends BaseController
{
    protected $contratoModel;
    protected $clienteModel;
    protected $negocioModel;
    protected $abonoModel;

    public function __construct()
    {
        $this->contratoModel = new ContratoModel();
        $this->clienteModel = new ClienteModel();
        $this->negocioModel = new NegocioModel();
        $this->abonoModel = new AbonoModel();
        helper('contrato');
    }

    protected function obtenerContratoNegocio(int $id): ?array
    {
        return $this->contratoModel->obtenerCompleto($id, (int) session()->get('negocio_id'));
    }

    public function index()
    {
        $negocioId = session()->get('negocio_id');
        $busqueda = $this->request->getGet('q');
        $estado = $this->request->getGet('estado');
        $estadosValidos = ['activo', 'vigente', 'redimido', 'vencido', 'vendido', 'renovado'];

        if ($estado !== null && $estado !== '' && ! in_array($estado, $estadosValidos, true)) {
            $estado = null;
        }

        return view('contratos/index', [
            'titulo' => 'Contratos',
            'contratos' => $this->contratoModel->listarConCliente($negocioId, $busqueda, 100, $estado ?: null),
            'busqueda' => $busqueda,
            'estado' => $estado ?: null,
        ]);
    }

    public function crear()
    {
        $negocio = $this->negocioModel->find(session()->get('negocio_id'));

        return view('contratos/crear', [
            'titulo' => 'Nuevo Contrato',
            'negocio' => $negocio,
            'siguiente_numero' => (int) $negocio['ultimo_numero'] + 1,
        ]);
    }

    public function guardar()
    {
        $negocioId = session()->get('negocio_id');
        $negocio = $this->negocioModel->find($negocioId);

        $rules = [
            'nombre' => 'required|min_length[3]|max_length[150]',
            'cedula' => 'required|min_length[5]|max_length[20]',
            'monto' => 'required',
            'fecha_inicio' => 'required|valid_date[Y-m-d]',
            'fecha_terminacion' => 'required|valid_date[Y-m-d]',
            'descripcion_articulo' => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $monto = parse_monto_colombiano($this->request->getPost('monto'));
        if ($monto === null || $monto <= 0) {
            return redirect()->back()->withInput()->with('error', 'Ingrese un valor válido para el monto (ej: 400.000).');
        }

        $fechaInicio = $this->request->getPost('fecha_inicio');
        $fechaTerminacion = $this->request->getPost('fecha_terminacion');

        if (strtotime($fechaTerminacion) < strtotime($fechaInicio)) {
            return redirect()->back()->withInput()->with('error', 'La fecha de terminación debe ser posterior a la de inicio.');
        }

        $cedula = trim($this->request->getPost('cedula'));
        $cliente = $this->clienteModel->buscarPorCedula($negocioId, $cedula);

        if ($cliente) {
            $this->clienteModel->update($cliente['id'], [
                'nombre' => trim($this->request->getPost('nombre')),
                'telefono' => $this->request->getPost('telefono'),
            ]);
            $clienteId = $cliente['id'];
        } else {
            $clienteId = $this->clienteModel->insert([
                'negocio_id' => $negocioId,
                'nombre' => trim($this->request->getPost('nombre')),
                'cedula' => $cedula,
                'telefono' => $this->request->getPost('telefono'),
            ]);
        }

        $numero = $this->negocioModel->siguienteNumero($negocioId);

        $contratoId = $this->contratoModel->insert([
            'negocio_id' => $negocioId,
            'cliente_id' => $clienteId,
            'usuario_id' => session()->get('usuario_id'),
            'numero' => $numero,
            'monto' => $monto,
            'saldo_capital' => $monto,
            'fecha_inicio' => $fechaInicio,
            'fecha_terminacion' => $fechaTerminacion,
            'fecha_ultimo_movimiento' => $fechaInicio,
            'descripcion_articulo' => trim($this->request->getPost('descripcion_articulo')),
            'estado' => 'vigente',
            'observaciones' => $this->request->getPost('observaciones'),
        ]);

        return redirect()->to(site_url('contratos/imprimir/' . $contratoId))
            ->with('success', 'Contrato N° ' . $numero . ' creado exitosamente.');
    }

    public function ver($id)
    {
        $negocioId = (int) session()->get('negocio_id');
        $contrato = $this->obtenerContratoNegocio((int) $id);
        if (!$contrato) {
            return redirect()->to(site_url('contratos'))->with('error', 'Contrato no encontrado.');
        }

        if (contrato_finalizado($contrato) && (float) ($contrato['saldo_capital'] ?? 0) > 0) {
            $this->contratoModel->update($id, ['saldo_capital' => 0]);
            $contrato['saldo_capital'] = 0;
        }

        $negocio = $this->negocioModel->find($negocioId);
        $abonos = $this->abonoModel->listarPorContrato((int) $id, $negocioId);
        $resumen = resumen_financiero_contrato($contrato, $negocio);

        return view('contratos/ver', [
            'titulo' => 'Contrato N° ' . $contrato['numero'],
            'contrato' => $contrato,
            'negocio' => $negocio,
            'resumen' => $resumen,
            'abonos' => $abonos,
            'puede_abono' => puede_registrar_abono($contrato),
            'fecha_cierre' => fecha_cierre_contrato($contrato, $abonos),
        ]);
    }

    public function imprimir($id)
    {
        $negocioId = session()->get('negocio_id');
        $contrato = $this->contratoModel->obtenerCompleto($id, $negocioId);
        if (!$contrato) {
            return redirect()->to(site_url('contratos'))->with('error', 'Contrato no encontrado.');
        }

        $negocio = $this->negocioModel->find($negocioId);

        return view('contratos/imprimir', [
            'contrato' => $contrato,
            'negocio' => $negocio,
        ]);
    }

    public function cambiarEstado($id)
    {
        $contrato = $this->contratoModel->obtenerCompleto($id, session()->get('negocio_id'));
        if (!$contrato) {
            return redirect()->to(site_url('contratos'))->with('error', 'Contrato no encontrado.');
        }

        $estado = $this->request->getPost('estado');
        $estadosValidos = ['vigente', 'renovado', 'redimido', 'vencido', 'vendido'];

        if (!in_array($estado, $estadosValidos, true)) {
            return redirect()->back()->with('error', 'Estado no válido.');
        }

        $update = ['estado' => $estado];

        if ($estado === 'redimido') {
            $update['saldo_capital'] = 0;
            $update['fecha_terminacion'] = date('Y-m-d');
        } elseif ($estado === 'vendido') {
            $update['saldo_capital'] = 0;
        }

        $this->contratoModel->update($id, $update);

        $mensajes = [
            'redimido' => 'Contrato marcado como redimido. El artículo ya no está en la compraventa.',
            'vendido' => 'Contrato marcado como vendido. El artículo ya no está disponible para el cliente.',
        ];

        return redirect()->back()->with(
            'success',
            $mensajes[$estado] ?? ('Estado actualizado a: ' . ucfirst($estado))
        );
    }

    public function buscarCliente()
    {
        $cedula = $this->request->getGet('cedula');
        $negocioId = session()->get('negocio_id');

        if (!$cedula) {
            return $this->response->setJSON(['found' => false]);
        }

        $cliente = $this->clienteModel->buscarPorCedula($negocioId, $cedula);

        if ($cliente) {
            return $this->response->setJSON([
                'found' => true,
                'nombre' => $cliente['nombre'],
                'telefono' => $cliente['telefono'],
            ]);
        }

        return $this->response->setJSON(['found' => false]);
    }

    public function sugerenciasBusqueda()
    {
        $q = trim($this->request->getGet('q') ?? '');
        $negocioId = (int) session()->get('negocio_id');

        if (strlen($q) < 2) {
            return $this->response->setJSON([]);
        }

        $resultados = $this->contratoModel->sugerenciasBusqueda($negocioId, $q);

        $sugerencias = array_map(static function (array $c) {
            return [
                'id' => (int) $c['id'],
                'numero' => (int) $c['numero'],
                'cliente' => $c['cliente_nombre'],
                'cedula' => $c['cliente_cedula'],
                'articulo' => $c['descripcion_articulo'],
                'estado' => $c['estado'],
                'texto' => 'N° ' . $c['numero'] . ' · ' . $c['cliente_nombre'] . ' · ' . $c['descripcion_articulo'],
            ];
        }, $resultados);

        return $this->response->setJSON($sugerencias);
    }

    public function simularAbono($id)
    {
        $contrato = $this->obtenerContratoNegocio((int) $id);
        if (!$contrato) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Contrato no encontrado']);
        }

        $negocio = $this->negocioModel->find(session()->get('negocio_id'));
        $monto = parse_monto_colombiano($this->request->getGet('monto'));
        $fecha = $this->request->getGet('fecha') ?: date('Y-m-d');

        if ($monto === null || $monto <= 0) {
            return $this->response->setJSON(['error' => 'Monto inválido']);
        }

        $sim = simular_abono_contrato($contrato, $negocio, $monto, $fecha);

        return $this->response->setJSON([
            'interes_devengado' => $sim['interes_devengado'],
            'interes_cobrado' => $sim['interes_cobrado'],
            'capital_abonado' => $sim['capital_abonado'],
            'interes_capitalizado' => $sim['interes_capitalizado'],
            'total_adeudado_antes' => $sim['total_adeudado_antes'],
            'saldo_capital_nuevo' => $sim['saldo_capital_nuevo'],
            'saldo_total_nuevo' => $sim['saldo_total_nuevo'],
            'dias_periodo' => $sim['dias_periodo'],
            'meses_periodo' => $sim['meses_periodo'],
            'fecha_terminacion_nueva' => $sim['fecha_terminacion_nueva'],
            'redimido' => $sim['redimido'],
            'formato' => [
                'total_adeudado' => formato_moneda($sim['total_adeudado_antes']),
                'interes' => formato_moneda($sim['interes_devengado']),
                'saldo_nuevo' => formato_moneda($sim['saldo_total_nuevo']),
                'nueva_vigencia' => formato_fecha_largo($sim['fecha_terminacion_nueva']),
            ],
        ]);
    }

    public function registrarAbono($id)
    {
        $negocioId = (int) session()->get('negocio_id');
        $contrato = $this->obtenerContratoNegocio((int) $id);
        if (!$contrato) {
            return redirect()->to(site_url('contratos'))->with('error', 'Contrato no encontrado.');
        }

        if (!puede_registrar_abono($contrato)) {
            return redirect()->back()->with('error', 'Este contrato no admite abonos.');
        }

        $monto = parse_monto_colombiano($this->request->getPost('monto'));
        $fechaAbono = $this->request->getPost('fecha_abono') ?: date('Y-m-d');

        if ($monto === null || $monto <= 0) {
            return redirect()->back()->with('error', 'Ingrese un monto de abono válido.');
        }

        $negocio = $this->negocioModel->find($negocioId);
        $sim = simular_abono_contrato($contrato, $negocio, $monto, $fechaAbono);

        $db = \Config\Database::connect();
        $db->transStart();

        $this->abonoModel->insert([
            'negocio_id' => $negocioId,
            'contrato_id' => (int) $id,
            'usuario_id' => session()->get('usuario_id'),
            'monto' => $monto,
            'interes_cobrado' => $sim['interes_cobrado'],
            'capital_abonado' => $sim['capital_abonado'],
            'interes_capitalizado' => $sim['interes_capitalizado'],
            'saldo_capital_anterior' => $sim['saldo_capital_anterior'],
            'saldo_capital_nuevo' => $sim['saldo_capital_nuevo'],
            'total_adeudado_antes' => $sim['total_adeudado_antes'],
            'saldo_total_nuevo' => $sim['saldo_total_nuevo'],
            'dias_periodo' => $sim['dias_periodo'],
            'tasa_mensual' => $sim['tasa_mensual'],
            'fecha_periodo_desde' => $sim['fecha_periodo_desde'],
            'fecha_abono' => $fechaAbono,
            'fecha_terminacion_anterior' => $sim['fecha_terminacion_anterior'],
            'fecha_terminacion_nueva' => $sim['fecha_terminacion_nueva'],
            'notas' => trim($this->request->getPost('notas') ?? ''),
        ]);

        $this->contratoModel->update($id, [
            'saldo_capital' => $sim['saldo_capital_nuevo'],
            'fecha_ultimo_movimiento' => $fechaAbono,
            'fecha_terminacion' => $sim['fecha_terminacion_nueva'],
            'estado' => $sim['nuevo_estado'],
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'No se pudo registrar el abono.');
        }

        $msg = $sim['redimido']
            ? '¡Artículo redimido! El cliente pagó el total del contrato.'
            : 'Abono registrado. Nuevo saldo: ' . formato_moneda($sim['saldo_total_nuevo'])
                . '. Vigencia hasta ' . formato_fecha_largo($sim['fecha_terminacion_nueva']) . '.';

        return redirect()->to(site_url('contratos/ver/' . $id))->with('success', $msg);
    }
}
