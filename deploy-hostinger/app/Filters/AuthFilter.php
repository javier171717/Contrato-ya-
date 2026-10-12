<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('platform_admin_id')) {
            return redirect()->to(site_url('admin/negocios'));
        }
        if (!session()->get('usuario_id')) {
            return redirect()->to(site_url('login'))->with('error', 'Debe iniciar sesión para continuar.');
        }

        $negocio = (new \App\Models\NegocioModel())->find(session()->get('negocio_id'));
        if (!$negocio || !(new \App\Models\NegocioModel())->puedeAccederPanel($negocio)) {
            session()->destroy();
            return redirect()->to(site_url('login'))->with('error', 'El acceso al panel de su negocio está suspendido.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
