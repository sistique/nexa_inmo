<?php

namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use stdClass;

abstract class _ctl_rel_comision extends _ctl_base
{
    protected string $entidad_relacion;
    protected string $label_relacion;

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array();
        $keys->selects = array();
        $campos = $this->campos_view_base(init_data: array(
            'inm_comision' => 'gamboamartin\\inmuebles',
            $this->entidad_relacion => 'gamboamartin\\inmuebles'
        ), keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campos', data: $campos);
        }
        return $campos;
    }

    private function selects(bool $modifica): array
    {
        $keys_selects = array();
        foreach (array('inm_comision_id' => 'Comision',
                       $this->entidad_relacion . '_id' => $this->label_relacion) as $key => $label) {
            $id_selected = $modifica ? (int)$this->row_upd->$key : -1;
            $keys_selects = $this->key_select(cols: 6, con_registros: true, filtro: array(), key: $key,
                keys_selects: $keys_selects, id_selected: $id_selected, label: $label);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al configurar ' . $label, data: $keys_selects);
            }
        }
        return $keys_selects;
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar alta', data: $alta, header: $header, ws: $ws);
        }
        $selects = $this->selects(modifica: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al configurar selects', data: $selects, header: $header, ws: $ws);
        }
        $inputs = $this->inputs(keys_selects: $selects);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar inputs', data: $inputs, header: $header, ws: $ws);
        }
        return $alta;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
        $modifica = $this->init_modifica();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar modifica', data: $modifica,
                header: $header, ws: $ws);
        }
        $selects = $this->selects(modifica: true);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al configurar selects', data: $selects, header: $header, ws: $ws);
        }
        $base = $this->base_upd(keys_selects: $selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar formulario', data: $base,
                header: $header, ws: $ws);
        }
        return $modifica;
    }
}
