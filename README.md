# Compraventa PWA - Sistema Multinegocios

Sistema PWA (Progressive Web App) para gestión de **contratos de compraventa con pacto de retroventa**, basado en el formato físico de recibos colombianos como el de Compraventa Valery.

## Características

- **Multinegocios**: cada establecimiento configura su nombre, NIT, dirección, logo y texto legal
- **Formulario simple**: solo ingresa los datos que antes se escribían a mano
- **Numeración automática** de contratos por negocio
- **Impresión** con plantilla idéntica al recibo físico
- **Búsqueda** por nombre, cédula, número o artículo
- **Panel de control** con contratos vigentes, vencidos y próximos a vencer
- **PWA instalable** en celular/tablet con soporte offline básico
- **Autocompletado** de clientes por cédula

## Requisitos

- PHP 7.2+ (XAMPP recomendado)
- MySQL / MariaDB
- Apache con `mod_rewrite`

## Instalación

1. Clonar o copiar el proyecto en `c:\xampp\htdocs\compraventa`

2. Importar la base de datos:
   ```bash
   mysql -u root < database/compraventa.sql
   ```
   O desde phpMyAdmin: importar `database/compraventa.sql`

3. Configurar `.env` (ya incluido para XAMPP):
   ```
   database.default.hostname = localhost
   database.default.database = compraventa
   database.default.username = root
   database.default.password =
   ```

4. Asegurar que Apache tenga `mod_rewrite` activo y acceder a:
   ```
   http://localhost/compraventa/public/
   ```

## Credenciales de prueba

| Campo     | Valor              |
|-----------|--------------------|
| Email     | admin@valery.com   |
| Contraseña| admin123           |

## Campos que se ingresan manualmente

| Campo                  | Ejemplo del recibo    |
|------------------------|-----------------------|
| Cédula del vendedor    | 1082337234            |
| Nombre del vendedor    | Jhonny Jimenez        |
| Descripción artículo   | 1 Bicicleta #27       |
| Valor                  | $400.000              |
| Fecha inicio           | 03/03/26              |
| Fecha terminación      | 03/06/26              |

El **número de contrato** se genera automáticamente (siguiente al último registrado).

## Estructura

```
app/
  Controllers/   Auth, Dashboard, Contratos, Negocios
  Models/        Negocio, Usuario, Cliente, Contrato
  Views/         Formularios, panel y plantilla de recibo
public/
  css/           Estilos app y recibo imprimible
  manifest.json  Configuración PWA
  sw.js          Service Worker
database/
  compraventa.sql
```

## Referencia del mercado

Sistemas comerciales como [SWAT ACV](https://swatingenieria.com/software-swat-acv/) ofrecen facturación electrónica, prórrogas y BI. Este sistema cubre el núcleo operativo: **generación de contratos y control de vencimientos**, extensible según necesidades futuras.

## Próximas mejoras sugeridas

- Prórrogas y abonos parciales
- Notificaciones SMS/WhatsApp de vencimiento
- Facturación electrónica DIAN
- Múltiples usuarios por negocio
- Reportes y exportación Excel
