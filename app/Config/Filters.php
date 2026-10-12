<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [
		'csrf'     => \CodeIgniter\Filters\CSRF::class,
		'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
		'honeypot' => \CodeIgniter\Filters\Honeypot::class,
		'auth'     => \App\Filters\AuthFilter::class,
		'platform' => \App\Filters\PlatformAuthFilter::class,
		'dueno'    => \App\Filters\DueñoFilter::class,
	];

	// Always applied before every request
	public $globals = [
		'before' => [
			//'honeypot'
			// 'csrf',
		],
		'after'  => [
			// toolbar solo en desarrollo (rompe en producción si writable no escribe)
			// 'toolbar',
		],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
	public $filters = [
		'platform' => [
			'before' => [
				'admin',
				'admin/*',
			],
		],
		'auth' => [
			'before' => [
				'dashboard',
				'dashboard/*',
				'contratos',
				'contratos/*',
				'negocio/*',
				'api/*',
				'cuenta',
				'cuenta/*',
				'usuarios',
				'usuarios/*',
				'finanzas',
				'finanzas/*',
			],
		],
		'dueno' => [
			'before' => [
				'negocio/*',
				'usuarios',
				'usuarios/*',
				'finanzas',
				'finanzas/*',
			],
		],
	];
}
