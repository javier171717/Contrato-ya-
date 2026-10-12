<?php namespace App\Controllers;

use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $negocioId = session()->get('negocio_id');

        return view('usuarios/index', [
            'titulo' => 'Empleados',
            'empleados' => $this->usuarioModel->listarEmpleados($negocioId),
        ]);
    }

    public function crear()
    {
        return view('usuarios/crear', [
            'titulo' => 'Nuevo empleado',
        ]);
    }

    public function guardar()
    {
        $negocioId = session()->get('negocio_id');

        $rules = [
            'nombre' => 'required|min_length[2]|max_length[150]',
            'username' => 'required|min_length[3]|max_length[80]|is_unique[usuarios.username]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = strtolower(trim($this->request->getPost('username')));
        if (!preg_match('/^[a-z0-9._-]+$/', $username)) {
            return redirect()->back()->withInput()->with('error', 'Usuario inválido. Use minúsculas, números, punto, guión o guión bajo.');
        }

        $email = trim($this->request->getPost('email') ?? '');
        if ($email === '') {
            $email = $username . '.' . $negocioId . '@interno.compraventa';
        } elseif ($this->usuarioModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('error', 'Ese correo ya está registrado.');
        }

        $this->usuarioModel->insert([
            'negocio_id' => $negocioId,
            'nombre' => trim($this->request->getPost('nombre')),
            'username' => $username,
            'email' => $email,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'rol' => 'empleado',
            'activo' => 1,
        ]);

        return redirect()->to(site_url('usuarios'))->with('success', 'Empleado creado correctamente.');
    }

    public function password($id)
    {
        $negocioId = session()->get('negocio_id');
        $empleado = $this->usuarioModel->find((int) $id);

        if (!$empleado || (int) $empleado['negocio_id'] !== (int) $negocioId || $empleado['rol'] !== 'empleado') {
            return redirect()->to(site_url('usuarios'))->with('error', 'Empleado no encontrado.');
        }

        return view('usuarios/password', [
            'titulo' => 'Cambiar contraseña',
            'empleado' => $empleado,
        ]);
    }

    public function actualizarPassword($id)
    {
        $negocioId = session()->get('negocio_id');
        $empleado = $this->usuarioModel->find((int) $id);

        if (!$empleado || (int) $empleado['negocio_id'] !== (int) $negocioId || $empleado['rol'] !== 'empleado') {
            return redirect()->to(site_url('usuarios'))->with('error', 'Empleado no encontrado.');
        }

        $password = $this->request->getPost('password');
        if (strlen($password) < 6) {
            return redirect()->back()->with('error', 'La contraseña debe tener al menos 6 caracteres.');
        }

        $this->usuarioModel->update($id, [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return redirect()->to(site_url('usuarios'))->with('success', 'Contraseña de ' . $empleado['nombre'] . ' actualizada.');
    }

    public function toggle($id)
    {
        $negocioId = session()->get('negocio_id');
        $empleado = $this->usuarioModel->find((int) $id);

        if (!$empleado || (int) $empleado['negocio_id'] !== (int) $negocioId || $empleado['rol'] !== 'empleado') {
            return redirect()->to(site_url('usuarios'))->with('error', 'Empleado no encontrado.');
        }

        $this->usuarioModel->update($id, ['activo' => (int) $empleado['activo'] === 1 ? 0 : 1]);

        $msg = (int) $empleado['activo'] === 1 ? 'Empleado desactivado.' : 'Empleado activado.';
        return redirect()->to(site_url('usuarios'))->with('success', $msg);
    }
}
