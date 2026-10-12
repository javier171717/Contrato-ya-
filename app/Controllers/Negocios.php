<?php namespace App\Controllers;

use App\Models\NegocioModel;
use App\Models\ContratoModel;

class Negocios extends BaseController
{
    public function configuracion()
    {
        if (session()->get('usuario_rol') !== 'admin') {
            return redirect()->to(site_url('dashboard'))->with('error', 'No tiene permisos para esta sección.');
        }

        $negocioId = session()->get('negocio_id');
        $negocio = (new NegocioModel())->find($negocioId);
        $contratoModel = new ContratoModel();

        return view('negocios/configuracion', [
            'titulo' => 'Configuración del Negocio',
            'negocio' => $negocio,
            'numero_maximo_contrato' => $contratoModel->numeroMaximo($negocioId),
        ]);
    }

    public function actualizar()
    {
        if (session()->get('usuario_rol') !== 'admin') {
            return redirect()->to(site_url('dashboard'))->with('error', 'No tiene permisos para esta sección.');
        }

        $negocioId = session()->get('negocio_id');
        $negocioModel = new NegocioModel();
        $contratoModel = new ContratoModel();

        $rules = [
            'nombre' => 'required|min_length[3]|max_length[150]',
            'nit' => 'required|max_length[30]',
            'direccion' => 'required|max_length[255]',
            'telefono' => 'required|max_length[50]',
            'ciudad' => 'required|max_length[100]',
            'departamento' => 'required|max_length[100]',
            'dias_plazo_default' => 'required|integer|greater_than[0]',
            'tasa_interes_mensual' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'ultimo_numero' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ultimoNumero = (int) $this->request->getPost('ultimo_numero');
        $numeroMaximo = $contratoModel->numeroMaximo($negocioId);

        if ($ultimoNumero < $numeroMaximo) {
            return redirect()->back()->withInput()->with(
                'error',
                'El último número no puede ser menor a ' . $numeroMaximo . ' porque ya existe un recibo con ese código.'
            );
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'nit' => $this->request->getPost('nit'),
            'direccion' => $this->request->getPost('direccion'),
            'telefono' => $this->request->getPost('telefono'),
            'ciudad' => $this->request->getPost('ciudad'),
            'departamento' => $this->request->getPost('departamento'),
            'representante_nombre' => $this->request->getPost('representante_nombre'),
            'representante_cc' => $this->request->getPost('representante_cc'),
            'dias_plazo_default' => $this->request->getPost('dias_plazo_default'),
            'tasa_interes_mensual' => $this->request->getPost('tasa_interes_mensual'),
            'ultimo_numero' => $ultimoNumero,
            'texto_legal' => $this->request->getPost('texto_legal'),
        ];

        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $nuevoNombre = 'logo_' . $negocioId . '_' . time() . '.' . $logo->getExtension();
            $logo->move(FCPATH . 'uploads/logos', $nuevoNombre);
            $data['logo'] = 'uploads/logos/' . $nuevoNombre;
        }

        $negocioModel->update($negocioId, $data);
        session()->set('negocio_nombre', $data['nombre']);

        return redirect()->to(site_url('negocio/configuracion'))->with('success', 'Configuración actualizada correctamente.');
    }
}
