<?php

if (!function_exists('es_dueño')) {
    function es_dueño(): bool
    {
        return session()->get('usuario_rol') === 'admin';
    }
}

if (!function_exists('es_empleado')) {
    function es_empleado(): bool
    {
        return session()->get('usuario_rol') === 'empleado';
    }
}

if (!function_exists('etiqueta_rol_usuario')) {
    function etiqueta_rol_usuario(?string $rol): string
    {
        if ($rol === 'admin') {
            return 'Dueño';
        }
        if ($rol === 'empleado') {
            return 'Empleado';
        }
        return $rol ? ucfirst($rol) : '—';
    }
}

if (!function_exists('texto_creador_contrato')) {
    function texto_creador_contrato(?array $contrato): string
    {
        if (empty($contrato['creador_nombre'])) {
            return '—';
        }
        return $contrato['creador_nombre'] . ' (' . etiqueta_rol_usuario($contrato['creador_rol'] ?? '') . ')';
    }
}
