<?php

if (!function_exists('parse_monto_colombiano')) {
    /**
     * Convierte montos en formato colombiano a entero (pesos).
     * Acepta: 400000, 400.000, 1.500.000, 400000,50
     */
    function parse_monto_colombiano($value): ?int
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $value = preg_replace('/[^\d,.]/', '', $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d+,\d{1,2}$/', $value)) {
            $value = str_replace(',', '.', $value);

            return (int) round((float) $value);
        }

        $value = preg_replace('/[.,]/', '', $value);
        if ($value === '' || !ctype_digit($value)) {
            return null;
        }

        return (int) $value;
    }
}

if (!function_exists('formato_monto_input')) {
    function formato_monto_input($monto): string
    {
        if ($monto === null || $monto === '') {
            return '';
        }

        $parsed = parse_monto_colombiano($monto);
        if ($parsed === null) {
            return (string) $monto;
        }

        return number_format($parsed, 0, ',', '.');
    }
}

if (!function_exists('formato_moneda')) {
    function formato_moneda($monto): string
    {
        return '$' . number_format((float) $monto, 0, ',', '.');
    }
}

if (!function_exists('formato_fecha_recibo')) {
    function formato_fecha_recibo(?string $fecha): string
    {
        if (!$fecha) {
            return '';
        }
        $ts = strtotime($fecha);
        return sprintf('%02d %02d %02d', date('d', $ts), date('m', $ts), date('y', $ts));
    }
}

if (!function_exists('formato_fecha_largo')) {
    function formato_fecha_largo(?string $fecha): string
    {
        if (!$fecha) {
            return '';
        }
        $meses = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $ts = strtotime($fecha);
        return date('d', $ts) . ' de ' . $meses[(int) date('n', $ts)] . ' de ' . date('Y', $ts);
    }
}

if (!function_exists('formato_datetime')) {
    function formato_datetime(?string $fecha): string
    {
        if (!$fecha) {
            return '—';
        }
        return date('d/m/Y H:i', strtotime($fecha));
    }
}

if (!function_exists('formato_datetime_input')) {
    function formato_datetime_input(?string $fecha): string
    {
        if (!$fecha) {
            return '';
        }
        return date('Y-m-d\TH:i', strtotime($fecha));
    }
}

if (!function_exists('dias_restantes')) {
    function dias_restantes(?string $fechaTerminacion): int
    {
        if (!$fechaTerminacion) {
            return 0;
        }
        $hoy = new DateTime('today');
        $fin = new DateTime($fechaTerminacion);
        return (int) $hoy->diff($fin)->format('%r%a');
    }
}

if (!function_exists('etiqueta_vencimiento_relativa')) {
    function etiqueta_vencimiento_relativa(?string $fechaTerminacion): string
    {
        $dias = dias_restantes($fechaTerminacion);
        if ($dias === 0) {
            return 'Vence hoy';
        }
        if ($dias === 1) {
            return 'Vence mañana';
        }
        if ($dias > 1) {
            return 'En ' . $dias . ' días';
        }
        if ($dias === -1) {
            return 'Venció ayer';
        }

        return 'Venció hace ' . abs($dias) . ' días';
    }
}

if (!function_exists('dias_entre_fechas')) {
    function dias_entre_fechas(string $desde, string $hasta): int
    {
        $inicio = new DateTime($desde);
        $fin = new DateTime($hasta);
        if ($fin < $inicio) {
            return 0;
        }
        return (int) $inicio->diff($fin)->days;
    }
}

if (!function_exists('tasa_interes_negocio')) {
    function tasa_interes_negocio(?array $negocio): float
    {
        $tasa = (float) ($negocio['tasa_interes_mensual'] ?? 10);
        return $tasa > 0 ? $tasa : 10.0;
    }
}

if (!function_exists('contrato_finalizado')) {
    function contrato_finalizado(array $contrato): bool
    {
        return in_array($contrato['estado'] ?? '', ['redimido', 'vendido'], true);
    }
}

if (!function_exists('saldo_capital_contrato')) {
    function saldo_capital_contrato(array $contrato): float
    {
        if (contrato_finalizado($contrato)) {
            return 0;
        }
        if (isset($contrato['saldo_capital']) && (float) $contrato['saldo_capital'] > 0) {
            return (float) $contrato['saldo_capital'];
        }
        return (float) $contrato['monto'];
    }
}

if (!function_exists('fecha_ultimo_movimiento_contrato')) {
    function fecha_ultimo_movimiento_contrato(array $contrato): string
    {
        return $contrato['fecha_ultimo_movimiento'] ?? $contrato['fecha_inicio'];
    }
}

if (!function_exists('dias_minimos_cobro_interes')) {
    /** Mínimo un mes de interés aunque el cliente venga el mismo día del empeño. */
    function dias_minimos_cobro_interes(): int
    {
        return 30;
    }
}

if (!function_exists('dias_cobrables_interes')) {
    function dias_cobrables_interes(string $desde, string $hasta): int
    {
        $dias = dias_entre_fechas($desde, $hasta);
        return max($dias, dias_minimos_cobro_interes());
    }
}

if (!function_exists('calcular_interes_periodo')) {
    /**
     * Interés proporcional: capital × (tasa%/100) × (días / 30)
     */
    function calcular_interes_periodo(float $capital, float $tasaMensual, int $dias): int
    {
        if ($capital <= 0 || $dias <= 0 || $tasaMensual <= 0) {
            return 0;
        }
        return (int) round($capital * ($tasaMensual / 100) * ($dias / 30));
    }
}

if (!function_exists('resumen_financiero_contrato')) {
    function resumen_financiero_contrato(array $contrato, array $negocio, ?string $fechaCorte = null): array
    {
        $plazo = (int) ($negocio['dias_plazo_default'] ?? 90);
        $montoOriginal = (float) $contrato['monto'];

        if (contrato_finalizado($contrato)) {
            return [
                'cerrado' => true,
                'estado_cierre' => $contrato['estado'],
                'saldo_capital' => 0,
                'monto_original' => $montoOriginal,
                'interes_devengado' => 0,
                'interes_mensual' => 0,
                'total_adeudado' => 0,
                'dias_periodo' => 0,
                'dias_transcurridos' => 0,
                'mes_minimo_aplicado' => false,
                'meses_periodo' => 0,
                'tasa_mensual' => tasa_interes_negocio($negocio),
                'fecha_desde' => null,
                'fecha_corte' => null,
                'fecha_vencimiento' => null,
                'dias_restantes' => null,
                'plazo_dias' => $plazo,
            ];
        }

        $fechaCorte = $fechaCorte ?? date('Y-m-d');
        $capital = saldo_capital_contrato($contrato);
        $tasa = tasa_interes_negocio($negocio);
        $desde = fecha_ultimo_movimiento_contrato($contrato);
        $diasTranscurridos = dias_entre_fechas($desde, $fechaCorte);
        $diasCobrados = dias_cobrables_interes($desde, $fechaCorte);
        $interes = calcular_interes_periodo($capital, $tasa, $diasCobrados);
        $interesMensual = calcular_interes_periodo($capital, $tasa, 30);
        $meses = round($diasCobrados / 30, 2);

        return [
            'cerrado' => false,
            'saldo_capital' => $capital,
            'monto_original' => (float) $contrato['monto'],
            'interes_devengado' => $interes,
            'interes_mensual' => $interesMensual,
            'total_adeudado' => $capital + $interes,
            'dias_periodo' => $diasCobrados,
            'dias_transcurridos' => $diasTranscurridos,
            'mes_minimo_aplicado' => $diasTranscurridos < dias_minimos_cobro_interes(),
            'meses_periodo' => $meses,
            'tasa_mensual' => $tasa,
            'fecha_desde' => $desde,
            'fecha_corte' => $fechaCorte,
            'fecha_vencimiento' => $contrato['fecha_terminacion'],
            'dias_restantes' => dias_restantes($contrato['fecha_terminacion']),
            'plazo_dias' => (int) ($negocio['dias_plazo_default'] ?? 90),
        ];
    }
}

if (!function_exists('simular_abono_contrato')) {
    function simular_abono_contrato(array $contrato, array $negocio, int $montoAbono, ?string $fechaAbono = null): array
    {
        $fechaAbono = $fechaAbono ?? date('Y-m-d');
        $resumen = resumen_financiero_contrato($contrato, $negocio, $fechaAbono);

        $capital = $resumen['saldo_capital'];
        $interes = $resumen['interes_devengado'];
        $totalAntes = $resumen['total_adeudado'];
        $plazo = $resumen['plazo_dias'];

        $aInteres = min($montoAbono, $interes);
        $restante = $montoAbono - $aInteres;
        $aCapital = min($restante, $capital);
        $interesPendiente = $interes - $aInteres;

        $nuevoCapital = $capital - $aCapital;
        if ($interesPendiente > 0) {
            $nuevoCapital += $interesPendiente;
        }

        $nuevoCapital = max(0, (int) round($nuevoCapital));
        $fechaTermAnterior = $contrato['fecha_terminacion'];
        $nuevoEstado = $contrato['estado'];

        if ($nuevoCapital <= 0) {
            $nuevoEstado = 'redimido';
            $nuevaFechaTerm = $fechaAbono;
            $saldoTotalNuevo = 0;
        } else {
            $nuevaFechaTerm = date('Y-m-d', strtotime($fechaAbono . " +{$plazo} days"));
            if (in_array($contrato['estado'], ['vigente', 'renovado', 'vencido'], true)) {
                $nuevoEstado = 'renovado';
            }
            $saldoTotalNuevo = $nuevoCapital;
        }

        return [
            'monto_abono' => $montoAbono,
            'fecha_abono' => $fechaAbono,
            'interes_cobrado' => $aInteres,
            'capital_abonado' => $aCapital,
            'interes_capitalizado' => $interesPendiente,
            'saldo_capital_anterior' => $capital,
            'saldo_capital_nuevo' => $nuevoCapital,
            'total_adeudado_antes' => $totalAntes,
            'saldo_total_nuevo' => $saldoTotalNuevo,
            'dias_periodo' => $resumen['dias_periodo'],
            'meses_periodo' => $resumen['meses_periodo'],
            'tasa_mensual' => $resumen['tasa_mensual'],
            'interes_devengado' => $interes,
            'fecha_periodo_desde' => $resumen['fecha_desde'],
            'fecha_terminacion_anterior' => $fechaTermAnterior,
            'fecha_terminacion_nueva' => $nuevaFechaTerm,
            'nuevo_estado' => $nuevoEstado,
            'redimido' => $nuevoCapital <= 0,
        ];
    }
}

if (!function_exists('puede_registrar_abono')) {
    function puede_registrar_abono(array $contrato): bool
    {
        return !contrato_finalizado($contrato)
            && in_array($contrato['estado'], ['vigente', 'renovado', 'vencido'], true)
            && saldo_capital_contrato($contrato) > 0;
    }
}

if (!function_exists('fecha_cierre_contrato')) {
    function fecha_cierre_contrato(array $contrato, array $abonos = []): ?string
    {
        if (!contrato_finalizado($contrato)) {
            return null;
        }
        if (!empty($abonos)) {
            return $abonos[0]['fecha_abono'];
        }
        return $contrato['fecha_ultimo_movimiento'] ?? $contrato['fecha_terminacion'] ?? null;
    }
}
