<?php

namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_rel_agente_proveedor;
use gamboamartin\inmuebles\html\inm_rel_agente_proveedor_html;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_rel_agente_proveedor extends _ctl_base
{
    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_rel_agente_proveedor(link: $link);
        $html_ = new inm_rel_agente_proveedor_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id:  $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array(
            'inm_rel_agente_proveedor_id' => array('titulo' => 'Id'),
            'com_agente_descripcion' => array('titulo' => 'Agente'),
            'gt_proveedor_razon_social' => array('titulo' => 'Proveedor')
        );
        $datatables->filtro = array('inm_rel_agente_proveedor.id', 'com_agente.descripcion', 'gt_proveedor.razon_social');

        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);
        $this->lista_get_data = true;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array();
        $keys->selects = array();
        $campos = $this->campos_view_base(init_data: array(
            'com_agente' => 'gamboamartin\\comercial',
            'gt_proveedor' => 'gamboamartin\\gastos'
        ), keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campos', data: $campos);
        }
        return $campos;
    }

    private function selects(bool $modifica): array
    {
        $keys_selects = array();
        foreach (array('com_agente_id' => 'Agente', 'gt_proveedor_id' => 'Proveedor') as $key => $label) {
            $id_selected = $modifica ? (int)$this->row_upd->$key : -1;
            $keys_selects = $this->key_select(cols: 6, con_registros: true, filtro: array(), key: $key,
                keys_selects: $keys_selects, id_selected: $id_selected, label: $label);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al configurar select', data: $keys_selects);
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
            return $this->retorno_error(mensaje: 'Error al inicializar modifica', data: $modifica, header: $header, ws: $ws);
        }
        $selects = $this->selects(modifica: true);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al configurar selects', data: $selects, header: $header, ws: $ws);
        }
        $base = $this->base_upd(keys_selects: $selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar formulario', data: $base, header: $header, ws: $ws);
        }
        return $modifica;
    }
}
