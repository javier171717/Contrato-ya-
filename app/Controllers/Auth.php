<?php namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\NegocioModel;
use App\Models\PlatformAdminModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('platform_admin_id')) {
            return redirect()->to(site_url('admin/negocios'));
        }
        if (session()->get('usuario_id')) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $username = strtolower(trim($this->request->getPost('username') ?? ''));
        $password = $this->request->getPost('password');

        if ($username === '') {
            return redirect()->back()->withInput()->with('error', 'Ingrese su usuario.');
        }

        $platformModel = new PlatformAdminModel();
        $platformAdmin = $platformModel->verificarLogin($username, $password);

        if ($platformAdmin) {
            session()->set([
                'platform_admin_id' => $platformAdmin['id'],
                'platform_admin_nombre' => $platformAdmin['nombre'],
                'platform_admin_username' => $platformAdmin['username'] ?? '',
                'es_plataforma' => true,
            ]);
            return redirect()->to(site_url('admin/negocios'))
                ->with('success', 'Bienvenido, ' . $platformAdmin['nombre']);
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->verificarLogin($username, $password);

        if (!$usuario) {
            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas.');
        }

        $negocioModel = new NegocioModel();
        $negocio = $negocioModel->find($usuario['negocio_id']);

        if (!$negocio || !$negocioModel->puedeAccederPanel($negocio)) {
            $motivo = 'El negocio no tiene acceso al panel.';
            if ($negocio && (int) $negocio['suspendido_admin'] === 1) {
                $motivo = 'El negocio fue suspendido por la plataforma. Contacte al administrador.';
            } elseif ($negocio && $negocio['estado_plan'] === 'suspendido_mora') {
                $motivo = 'El plan está suspendido por mora. Regularice el pago con la plataforma.';
            } elseif ($negocio && $negocio['estado_plan'] === 'prueba' && !empty($negocio['fin_prueba']) && strtotime($negocio['fin_prueba']) < time()) {
                $motivo = 'El período de prueba ha vencido. Contacte al administrador de la plataforma.';
            }
            return redirect()->back()->with('error', $motivo);
        }

        session()->set([
            'usuario_id' => $usuario['id'],
            'usuario_nombre' => $usuario['nombre'],
            'usuario_rol' => $usuario['rol'],
            'negocio_id' => $negocio['id'],
            'negocio_nombre' => $negocio['nombre'],
            'es_plataforma' => false,
        ]);

        return redirect()->to(site_url('dashboard'))->with('success', 'Bienvenido, ' . $usuario['nombre']);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'))->with('success', 'Sesión cerrada correctamente.');
    }
}
