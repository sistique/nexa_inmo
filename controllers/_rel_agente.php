<?php

namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\_rel_agente as modelo_rel_agente;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\html_controler;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class _rel_agente extends _ctl_base
{
    private string $entidad;
    private string $namespace_entidad;
    private string $label_entidad;

    public function __construct(PDO $link, html $html, stdClass $paths_conf, modelo_rel_agente $modelo,
                                string $entidad, string $namespace_entidad, string $label_entidad,
                                string $campo_lista)
    {
        $this->entidad = $entidad;
        $this->namespace_entidad = $namespace_entidad;
        $this->label_entidad = $label_entidad;

        $datatables = new stdClass();
        $datatables->columns = array(
            $modelo->tabla.'_id' => array('titulo' => 'Id'),
            'com_agente_descripcion' => array('titulo' => 'Agente'),
            $entidad.'_'.$campo_lista => array('titulo' => $label_entidad)
        );
        $datatables->filtro = array($modelo->tabla.'.id', 'com_agente.descripcion', $entidad.'.'.$campo_lista);

        parent::__construct(html: new html_controler(html: $html), link: $link, modelo: $modelo,
            obj_link: new links_menu(link: $link, registro_id: $this->registro_id), datatables: $datatables,
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
            $this->entidad => $this->namespace_entidad
        ), keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campos', data: $campos);
        }
        return $campos;
    }

    private function selects(bool $modifica): array
    {
        $keys_selects = array();
        foreach (array('com_agente_id' => 'Agente', $this->entidad.'_id' => $this->label_entidad) as $key => $label) {
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
