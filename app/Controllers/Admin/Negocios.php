<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NegocioModel;
use App\Models\UsuarioModel;

class Negocios extends BaseController
{
    protected $negocioModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->negocioModel = new NegocioModel();
        $this->usuarioModel = new UsuarioModel();
        helper(['contrato', 'form']);
    }

    public function index()
    {
        return view('admin/negocios/index', [
            'titulo' => 'Negocios',
            'negocios' => $this->negocioModel->listarConAdmin(),
        ]);
    }

    public function crear()
    {
        return view('admin/negocios/crear', [
            'titulo' => 'Alta de negocio y dueño',
            'texto_legal_default' => $this->textoLegalDefault(),
        ]);
    }

    public function guardar()
    {
        $rules = [
            'nombre' => 'required|min_length[2]|max_length[150]',
            'nit' => 'required|max_length[30]',
            'direccion' => 'required|max_length[255]',
            'telefono' => 'required|max_length[50]',
            'ciudad' => 'required|max_length[100]',
            'departamento' => 'required|max_length[100]',
            'admin_nombre' => 'required|min_length[2]|max_length[150]',
            'admin_email' => 'required|valid_email|is_unique[usuarios.email]',
            'admin_password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = $this->negocioModel->generarSlug($this->request->getPost('nombre'));
        $finPrueba = date('Y-m-d H:i:s', strtotime('+14 days'));

        $negocioId = $this->negocioModel->insert([
            'nombre' => trim($this->request->getPost('nombre')),
            'slug' => $slug,
            'nit' => trim($this->request->getPost('nit')),
            'direccion' => trim($this->request->getPost('direccion')),
            'telefono' => trim($this->request->getPost('telefono')),
            'ciudad' => trim($this->request->getPost('ciudad')),
            'departamento' => trim($this->request->getPost('departamento')),
            'representante_nombre' => trim($this->request->getPost('admin_nombre')),
            'dias_plazo_default' => 90,
            'ultimo_numero' => 0,
            'texto_legal' => $this->textoLegalDefault(),
            'activo' => 1,
            'estado_plan' => 'prueba',
            'fin_prueba' => $finPrueba,
            'suspendido_admin' => 0,
        ]);

        $username = strtolower(trim($this->request->getPost('admin_username') ?? ''));
        if ($username === '') {
            $username = strstr($this->request->getPost('admin_email'), '@', true) ?: 'admin';
        }
        if (!$this->usuarioModel->usernameDisponible($username)) {
            return redirect()->back()->withInput()->with('error', 'El nombre de usuario ya está en uso.');
        }

        $this->usuarioModel->insert([
            'negocio_id' => $negocioId,
            'nombre' => trim($this->request->getPost('admin_nombre')),
            'username' => $username,
            'email' => trim($this->request->getPost('admin_email')),
            'password' => password_hash($this->request->getPost('admin_password'), PASSWORD_DEFAULT),
            'rol' => 'admin',
            'activo' => 1,
        ]);

        return redirect()->to(site_url('admin/negocios/ver/' . $negocioId))
            ->with('success', 'Negocio creado con 14 días de prueba.');
    }

    public function ver($id)
    {
        $negocio = $this->negocioModel->obtenerConAdmin((int) $id);
        if (!$negocio) {
            return redirect()->to(site_url('admin/negocios'))->with('error', 'Negocio no encontrado.');
        }

        $tab = $this->request->getGet('tab') ?: 'plan';
        if (!in_array($tab, ['plan', 'negocio', 'admin', 'dueno', 'password', 'eliminar'], true)) {
            $tab = 'plan';
        }

        return view('admin/negocios/ver', [
            'titulo' => $negocio['nombre'],
            'negocio' => $negocio,
            'tab' => $tab,
        ]);
    }

    public function actualizarPlan($id)
    {
        $negocio = $this->negocioModel->find((int) $id);
        if (!$negocio) {
            return redirect()->to(site_url('admin/negocios'))->with('error', 'Negocio no encontrado.');
        }

        $data = [
            'estado_plan' => $this->request->getPost('estado_plan'),
            'plan_codigo' => $this->request->getPost('plan_codigo') ?: null,
            'fin_prueba' => $this->parseDateTime($this->request->getPost('fin_prueba')),
            'proximo_cobro' => $this->parseDateTime($this->request->getPost('proximo_cobro')),
        ];

        if (!in_array($data['estado_plan'], ['prueba', 'activo', 'suspendido_mora'], true)) {
            return redirect()->back()->with('error', 'Estado de plan no válido.');
        }

        $this->negocioModel->update($id, $data);

        return redirect()->to(site_url('admin/negocios/ver/' . $id . '?tab=plan'))
            ->with('success', 'Plan y fechas actualizados.');
    }

    public function actualizarNegocio($id)
    {
        $negocio = $this->negocioModel->find((int) $id);
        if (!$negocio) {
            return redirect()->to(site_url('admin/negocios'))->with('error', 'Negocio no encontrado.');
        }

        $rules = [
            'nombre' => 'required|min_length[2]|max_length[150]',
            'nit' => 'required|max_length[30]',
            'direccion' => 'required|max_length[255]',
            'telefono' => 'required|max_length[50]',
            'ciudad' => 'required|max_length[100]',
            'departamento' => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->negocioModel->update($id, [
            'nombre' => trim($this->request->getPost('nombre')),
            'nit' => trim($this->request->getPost('nit')),
            'direccion' => trim($this->request->getPost('direccion')),
            'telefono' => trim($this->request->getPost('telefono')),
            'ciudad' => trim($this->request->getPost('ciudad')),
            'departamento' => trim($this->request->getPost('departamento')),
            'dias_plazo_default' => (int) $this->request->getPost('dias_plazo_default') ?: 90,
        ]);

        return redirect()->to(site_url('admin/negocios/ver/' . $id . '?tab=negocio'))
            ->with('success', 'Datos del negocio actualizados.');
    }

    public function cambiarUsername($id)
    {
        $negocio = $this->negocioModel->find((int) $id);
        if (!$negocio) {
            return redirect()->to(site_url('admin/negocios'))->with('error', 'Negocio no encontrado.');
        }

        $admin = $this->usuarioModel->adminDelNegocio((int) $id);
        if (!$admin) {
            return redirect()->back()->with('error', 'No hay administrador asignado.');
        }

        $username = strtolower(trim($this->request->getPost('admin_username')));
        if (strlen($username) < 3) {
            return redirect()->back()->with('error', 'El usuario debe tener al menos 3 caracteres.');
        }
        if (!preg_match('/^[a-z0-9._-]+$/', $username)) {
            return redirect()->back()->with('error', 'Solo letras minúsculas, números, punto, guión y guión bajo.');
        }
        if (!$this->usuarioModel->usernameDisponible($username, (int) $admin['id'])) {
            return redirect()->back()->with('error', 'Ese nombre de usuario ya está en uso.');
        }

        $this->usuarioModel->update($admin['id'], ['username' => $username]);

        return redirect()->to(site_url('admin/negocios/ver/' . $id . '?tab=admin'))
            ->with('success', 'Usuario de inicio de sesión actualizado.');
    }

    public function cambiarDatosDueno($id)
    {
        $negocio = $this->negocioModel->find((int) $id);
        if (!$negocio) {
            return redirect()->to(site_url('admin/negocios'))->with('error', 'Negocio no encontrado.');
        }

        $admin = $this->usuarioModel->adminDelNegocio((int) $id);
        if (!$admin) {
            return redirect()->back()->with('error', 'No hay administrador asignado.');
        }

        $rules = [
            'admin_nombre' => 'required|min_length[2]|max_length[150]',
            'admin_email' => "required|valid_email|is_unique[usuarios.email,id,{$admin['id']}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->usuarioModel->update($admin['id'], [
            'nombre' => trim($this->request->getPost('admin_nombre')),
            'email' => trim($this->request->getPost('admin_email')),
        ]);

        return redirect()->to(site_url('admin/negocios/ver/' . $id . '?tab=dueno'))
            ->with('success', 'Datos del dueño actualizados.');
    }

    public function nuevaPassword($id)
    {
        $negocio = $this->negocioModel->find((int) $id);
        if (!$negocio) {
            return redirect()->to(site_url('admin/negocios'))->with('error', 'Negocio no encontrado.');
        }

        $admin = $this->usuarioModel->adminDelNegocio((int) $id);
        if (!$admin) {
            return redirect()->back()->with('error', 'No hay administrador asignado.');
        }

        $password = $this->request->getPost('admin_password');
        if (strlen($password) < 6) {
            return redirect()->back()->with('error', 'La contraseña debe tener al menos 6 caracteres.');
        }

        $this->usuarioModel->update($admin['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return redirect()->to(site_url('admin/negocios/ver/' . $id . '?tab=password'))
            ->with('success', 'Contraseña del administrador actualizada.');
    }

    public function toggleSuspension($id)
    {
        $negocio = $this->negocioModel->find((int) $id);
        if (!$negocio) {
            return redirect()->to(site_url('admin/negocios'))->with('error', 'Negocio no encontrado.');
        }

        $nuevo = (int) $negocio['suspendido_admin'] === 1 ? 0 : 1;
        $this->negocioModel->update($id, ['suspendido_admin' => $nuevo]);

        $msg = $nuevo ? 'Negocio suspendido por decisión administrativa.' : 'Negocio reactivado.';
        return redirect()->to(site_url('admin/negocios/ver/' . $id))->with('success', $msg);
    }

    public function eliminar($id)
    {
        $negocio = $this->negocioModel->find((int) $id);
        if (!$negocio) {
            return redirect()->to(site_url('admin/negocios'))->with('error', 'Negocio no encontrado.');
        }

        if ($this->request->getPost('confirmar') !== $negocio['slug']) {
            return redirect()->back()->with('error', 'Escriba el slug del negocio para confirmar la eliminación.');
        }

        $this->negocioModel->delete($id);

        return redirect()->to(site_url('admin/negocios'))
            ->with('success', 'Negocio y todos sus datos eliminados.');
    }

    private function parseDateTime(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $ts = strtotime($value);
        return $ts ? date('Y-m-d H:i:s', $ts) : null;
    }

    private function textoLegalDefault(): string
    {
        return 'Declaro bajo la gravedad del juramento que soy el propietario exclusivo de los bienes objeto de este contrato, que los mismos son de procedencia lícita y que no están sujetos a gravamen, embargo ni limitación alguna de dominio.

El presente contrato se celebra con sujeción al Pacto de Retroventa consagrado en el Artículo 1939 del Código Civil Colombiano, en virtud del cual me reservo el derecho de recuperar los bienes vendidos, reembolsando al comprador la suma de dinero que por ellos se me haya entregado.

El comprador queda exonerado de toda responsabilidad por el deterioro, pérdida, hurto, incendio o falta de funcionamiento de los bienes vendidos, así como por cualquier caso fortuito o fuerza mayor.

El derecho de retroventa es personalísimo e intransferible, no pudiendo cederse a terceros bajo ninguna circunstancia.

En caso de no ejercer la acción de retroventa dentro del plazo pactado, la venta se tendrá por pura y simple, quedando el comprador facultado para disponer libremente de los bienes.';
    }
}
