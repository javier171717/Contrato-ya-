<?php namespace Config;

$routes = Services::routes();

if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('login');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Público
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Super Admin - Plataforma
$routes->get('admin', 'Admin\Dashboard::index');
$routes->get('admin/cuenta', 'Admin\Perfil::index');
$routes->post('admin/cuenta/usuario', 'Admin\Perfil::actualizarUsuario');
$routes->post('admin/cuenta/password', 'Admin\Perfil::actualizarPassword');
$routes->get('admin/negocios', 'Admin\Negocios::index');
$routes->get('admin/negocios/crear', 'Admin\Negocios::crear');
$routes->post('admin/negocios/guardar', 'Admin\Negocios::guardar');
$routes->get('admin/negocios/ver/(:num)', 'Admin\Negocios::ver/$1');
$routes->post('admin/negocios/plan/(:num)', 'Admin\Negocios::actualizarPlan/$1');
$routes->post('admin/negocios/negocio/(:num)', 'Admin\Negocios::actualizarNegocio/$1');
$routes->post('admin/negocios/admin/(:num)', 'Admin\Negocios::cambiarUsername/$1');
$routes->post('admin/negocios/dueno/(:num)', 'Admin\Negocios::cambiarDatosDueno/$1');
$routes->post('admin/negocios/password/(:num)', 'Admin\Negocios::nuevaPassword/$1');
$routes->get('admin/negocios/toggle/(:num)', 'Admin\Negocios::toggleSuspension/$1');
$routes->post('admin/negocios/eliminar/(:num)', 'Admin\Negocios::eliminar/$1');

// Dashboard negocio
$routes->get('dashboard', 'Dashboard::index');

// Contratos
$routes->get('contratos', 'Contratos::index');
$routes->get('contratos/crear', 'Contratos::crear');
$routes->post('contratos/guardar', 'Contratos::guardar');
$routes->get('contratos/ver/(:num)', 'Contratos::ver/$1');
$routes->get('contratos/imprimir/(:num)', 'Contratos::imprimir/$1');
$routes->post('contratos/estado/(:num)', 'Contratos::cambiarEstado/$1');
$routes->post('contratos/abono/(:num)', 'Contratos::registrarAbono/$1');
$routes->get('api/cliente', 'Contratos::buscarCliente');
$routes->get('api/contratos/sugerencias', 'Contratos::sugerenciasBusqueda');
$routes->get('api/contratos/(:num)/simular-abono', 'Contratos::simularAbono/$1');

// Negocio (solo dueño)
$routes->get('negocio/configuracion', 'Negocios::configuracion');
$routes->post('negocio/actualizar', 'Negocios::actualizar');

// Mi cuenta (dueño y empleados)
$routes->get('cuenta', 'Perfil::index');
$routes->post('cuenta/password', 'Perfil::actualizarPassword');

// Empleados (solo dueño)
$routes->get('usuarios', 'Usuarios::index');
$routes->get('usuarios/crear', 'Usuarios::crear');
$routes->post('usuarios/guardar', 'Usuarios::guardar');
$routes->get('usuarios/password/(:num)', 'Usuarios::password/$1');
$routes->post('usuarios/password/(:num)', 'Usuarios::actualizarPassword/$1');
$routes->get('usuarios/toggle/(:num)', 'Usuarios::toggle/$1');

// Finanzas
$routes->get('finanzas', 'Finanzas::index');
$routes->get('finanzas/gastos', 'Finanzas::gastos');
$routes->post('finanzas/gastos/guardar', 'Finanzas::guardarGasto');
$routes->get('finanzas/cierre', 'Finanzas::cierre');

$routes->setAutoRoute(false);

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
