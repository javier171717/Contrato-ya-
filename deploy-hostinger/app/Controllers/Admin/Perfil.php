<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PlatformAdminModel;

class Perfil extends BaseController
{
    public function index()
    {
        $admin = (new PlatformAdminModel())->find(session()->get('platform_admin_id'));
        if (!$admin) {
            return redirect()->to(site_url('login'));
        }

        return view('admin/perfil/index', [
            'titulo' => 'Mi cuenta',
            'admin' => $admin,
        ]);
    }

    public function actualizarUsuario()
    {
        $id = (int) session()->get('platform_admin_id');
        $model = new PlatformAdminModel();
        $admin = $model->find($id);
        if (!$admin) {
            return redirect()->to(site_url('login'));
        }

        $username = strtolower(trim($this->request->getPost('username')));
        if (strlen($username) < 3) {
            return redirect()->back()->with('error', 'El usuario debe tener al menos 3 caracteres.');
        }
        if (!preg_match('/^[a-z0-9._-]+$/', $username)) {
            return redirect()->back()->with('error', 'Solo letras minúsculas, números, punto, guión y guión bajo.');
        }
        if (!$model->usernameDisponible($username, $id)) {
            return redirect()->back()->with('error', 'Ese nombre de usuario ya está en uso.');
        }

        $model->update($id, ['username' => $username]);
        session()->set('platform_admin_username', $username);

        return redirect()->to(site_url('admin/cuenta'))->with('success', 'Usuario de inicio de sesión actualizado.');
    }

    public function actualizarPassword()
    {
        $id = (int) session()->get('platform_admin_id');
        $model = new PlatformAdminModel();
        $admin = $model->find($id);
        if (!$admin) {
            return redirect()->to(site_url('login'));
        }

        $actual = $this->request->getPost('password_actual');
        $nueva = $this->request->getPost('password_nueva');
        $confirmar = $this->request->getPost('password_confirmar');

        if (!password_verify($actual, $admin['password'])) {
            return redirect()->back()->with('error', 'La contraseña actual no es correcta.');
        }
        if (strlen($nueva) < 6) {
            return redirect()->back()->with('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
        }
        if ($nueva !== $confirmar) {
            return redirect()->back()->with('error', 'La confirmación de contraseña no coincide.');
        }

        $model->update($id, ['password' => password_hash($nueva, PASSWORD_DEFAULT)]);

        return redirect()->to(site_url('admin/cuenta'))->with('success', 'Contraseña actualizada correctamente.');
    }
}
