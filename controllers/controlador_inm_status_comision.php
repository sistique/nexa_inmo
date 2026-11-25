<?php

namespace gamboamartin\inmuebles\controllers;

use base\controller\init;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_status_comision_html;
use gamboamartin\inmuebles\models\inm_status_comision;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_status_comision extends _ctl_base
{
    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $datatables = new stdClass();
        $datatables->columns = array(
            'inm_status_comision_id' => array('titulo' => 'Id'),
            'inm_status_comision_descripcion' => array('titulo' => 'Descripcion')
        );
        $datatables->filtro = array('inm_status_comision.id', 'inm_status_comision.descripcion');

        parent::__construct(html: new inm_status_comision_html(html: $html), link: $link,
            modelo: new inm_status_comision(link: $link),
            obj_link: new links_menu(link: $link, registro_id: $this->registro_id),
            datatables: $datatables, paths_conf: $paths_conf);
        $this->lista_get_data = true;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('descripcion');
        $keys->selects = array();
        $campos = $this->campos_view_base(init_data: array(), keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campos', data: $campos);
        }
        return $campos;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'descripcion',
            keys_selects: $keys_selects, place_holder: 'Descripcion');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar descripcion', data: $keys_selects);
        }
        return $keys_selects;
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar alta', data: $alta, header: $header, ws: $ws);
        }
        $inputs = $this->inputs(keys_selects: array());
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
        $base = $this->base_upd(keys_selects: array(), params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar formulario', data: $base,
                header: $header, ws: $ws);
        }
        return $modifica;
    }
}
