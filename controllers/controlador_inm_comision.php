<?php

namespace gamboamartin\inmuebles\controllers;

use base\controller\init;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_comision_html;
use gamboamartin\inmuebles\models\inm_comision;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_comision extends _ctl_base
{
    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $datatables = new stdClass();
        $datatables->columns = array(
            'inm_comision_id' => array('titulo' => 'Id'),
            'inm_comision_descripcion' => array('titulo' => 'Descripcion'),
            'em_empleado_descripcion' => array('titulo' => 'Empleado'),
            'inm_periodo_comision_descripcion_select' => array('titulo' => 'Periodo'),
            'inm_conf_comision_descripcion' => array('titulo' => 'Configuracion'),
            'inm_status_comision_descripcion' => array('titulo' => 'Estatus'),
            'inm_comision_base_comision' => array('titulo' => 'Base'),
            'inm_comision_monto_pago' => array('titulo' => 'Monto de Pago')
        );
        $datatables->filtro = array('inm_comision.id', 'inm_comision.descripcion', 'em_empleado.descripcion',
            'inm_periodo_comision.descripcion_select', 'inm_conf_comision.descripcion',
            'inm_status_comision.descripcion', 'inm_comision.base_comision', 'inm_comision.monto_pago');

        parent::__construct(html: new inm_comision_html(html: $html), link: $link,
            modelo: new inm_comision(link: $link),
            obj_link: new links_menu(link: $link, registro_id: $this->registro_id),
            datatables: $datatables, paths_conf: $paths_conf);
        $this->lista_get_data = true;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('descripcion', 'base_comision', 'monto_pago');
        $keys->selects = array();
        $campos = $this->campos_view_base(init_data: array(
            'em_empleado' => 'gamboamartin\\empleado',
            'inm_periodo_comision' => 'gamboamartin\\inmuebles',
            'inm_conf_comision' => 'gamboamartin\\inmuebles',
            'inm_status_comision' => 'gamboamartin\\inmuebles'
        ), keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campos', data: $campos);
        }
        return $campos;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        foreach (array('descripcion' => 'Descripcion', 'base_comision' => 'Base de Comision',
                       'monto_pago' => 'Monto de Pago') as $key => $label) {
            $keys_selects = (new init())->key_select_txt(cols: $key === 'descripcion' ? 12 : 6,
                key: $key, keys_selects: $keys_selects, place_holder: $label);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al maquetar ' . $key, data: $keys_selects);
            }
        }
        return $keys_selects;
    }

    private function selects(bool $modifica): array
    {
        $keys_selects = array();
        foreach (array(
            'em_empleado_id' => 'Empleado',
            'inm_periodo_comision_id' => 'Periodo',
            'inm_conf_comision_id' => 'Configuracion de Comision',
            'inm_status_comision_id' => 'Estatus de Comision'
        ) as $key => $label) {
            $id_selected = $modifica ? (int)$this->row_upd->$key : -1;
            $columns_ds = $key === 'inm_periodo_comision_id' ? array('inm_periodo_comision_descripcion_select') : array();
            $keys_selects = $this->key_select(cols: 6, con_registros: true, filtro: array(), key: $key,
                keys_selects: $keys_selects, id_selected: $id_selected, label: $label, columns_ds: $columns_ds);
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
