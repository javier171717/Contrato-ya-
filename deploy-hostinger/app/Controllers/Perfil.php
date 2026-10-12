<?php namespace App\Controllers;

use App\Models\UsuarioModel;

class Perfil extends BaseController
{
    public function index()
    {
        $usuario = (new UsuarioModel())->find(session()->get('usuario_id'));
        if (!$usuario) {
            return redirect()->to(site_url('login'));
        }

        return view('perfil/index', [
            'titulo' => 'Mi cuenta',
            'usuario' => $usuario,
        ]);
    }

    public function actualizarPassword()
    {
        $id = (int) session()->get('usuario_id');
        $model = new UsuarioModel();
        $usuario = $model->find($id);
        if (!$usuario) {
            return redirect()->to(site_url('login'));
        }

        $actual = $this->request->getPost('password_actual');
        $nueva = $this->request->getPost('password_nueva');
        $confirmar = $this->request->getPost('password_confirmar');

        if (!password_verify($actual, $usuario['password'])) {
            return redirect()->back()->with('error', 'La contraseña actual no es correcta.');
        }
        if (strlen($nueva) < 6) {
            return redirect()->back()->with('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
        }
        if ($nueva !== $confirmar) {
            return redirect()->back()->with('error', 'La confirmación no coincide.');
        }

        $model->update($id, ['password' => password_hash($nueva, PASSWORD_DEFAULT)]);

        return redirect()->to(site_url('cuenta'))->with('success', 'Contraseña actualizada correctamente.');
    }
}
