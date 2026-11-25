<?php

namespace gamboamartin\inmuebles\controllers;

use base\controller\init;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_conf_comision_html;
use gamboamartin\inmuebles\models\inm_conf_comision;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_conf_comision extends _ctl_base
{
    public function __construct(
        PDO $link,
        html $html = new \gamboamartin\template_1\html(),
        stdClass $paths_conf = new stdClass()
    ) {
        $modelo = new inm_conf_comision(link: $link);
        $html_ = new inm_conf_comision_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);
        $datatables = $this->init_datatable();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable', data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(
            html: $html_,
            link: $link,
            modelo: $modelo,
            obj_link: $obj_link,
            datatables: $datatables,
            paths_conf: $paths_conf
        );
        $this->lista_get_data = true;
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar alta', data: $r_alta, header: $header, ws: $ws);
        }

        $this->row_upd->predeterminado = 'inactivo';
        $keys_selects = $this->keys_selects(id_tipo_comision: -1, id_tipo_agente: -1);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al maquetar relaciones',
                data: $keys_selects,
                header: $header,
                ws: $ws
            );
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        $this->integra_input_fecha(campo: 'fecha_inicial', label: 'Fecha Inicial');
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar fecha inicial',
                data: $this->inputs,
                header: $header,
                ws: $ws
            );
        }
        $this->integra_input_fecha(campo: 'fecha_final', label: 'Fecha Final');
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar fecha final',
                data: $this->inputs,
                header: $header,
                ws: $ws
            );
        }

        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('descripcion', 'porcentaje_pago', 'monto_pago_fijo', 'predeterminado');
        $keys->selects = array();
        $init_data = array(
            'com_tipo_agente' => 'gamboamartin\\comercial',
            'inm_tipo_comision' => 'gamboamartin\\inmuebles'
        );

        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica = $this->init_modifica();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',
                data: $r_modifica,
                header: $header,
                ws: $ws
            );
        }

        $keys_selects = $this->keys_selects(
            id_tipo_comision: (int)($this->row_upd->inm_tipo_comision_id ?? -1),
            id_tipo_agente: (int)($this->row_upd->com_tipo_agente_id ?? -1)
        );
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al maquetar relaciones',
                data: $keys_selects,
                header: $header,
                ws: $ws
            );
        }

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        $this->integra_input_fecha(campo: 'fecha_inicial', label: 'Fecha Inicial');
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar fecha inicial',
                data: $this->inputs,
                header: $header,
                ws: $ws
            );
        }
        $this->integra_input_fecha(campo: 'fecha_final', label: 'Fecha Final');
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar fecha final',
                data: $this->inputs,
                header: $header,
                ws: $ws
            );
        }

        return $r_modifica;
    }

    private function keys_selects(int $id_tipo_comision, int $id_tipo_agente): array
    {
        $keys_selects = $this->key_select(
            cols: 6,
            con_registros: true,
            filtro: array(),
            key: 'inm_tipo_comision_id',
            keys_selects: array(),
            id_selected: $id_tipo_comision,
            label: 'Tipo de Comision',
            required: false
        );
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar tipo de comision', data: $keys_selects);
        }

        $keys_selects = $this->key_select(
            cols: 6,
            con_registros: true,
            filtro: array(),
            key: 'com_tipo_agente_id',
            keys_selects: $keys_selects,
            id_selected: $id_tipo_agente,
            label: 'Tipo de Agente',
            required: false
        );
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar tipo de agente', data: $keys_selects);
        }

        return $keys_selects;
    }

    private function integra_input_fecha(string $campo, string $label): void
    {
        $value = $this->row_upd->$campo ?? '';
        $fecha = $this->html->input_fecha(
            cols: 6,
            row_upd: $this->row_upd,
            value_vacio: true,
            name: $campo,
            place_holder: $label,
            required: false,
            value: $value
        );
        if (errores::$error) {
            $this->errores->error(mensaje: 'Error al generar input fecha', data: $fecha);
            return;
        }

        $this->inputs->$campo = $fecha;
    }

    private function init_datatable(): stdClass
    {
        $columns['inm_conf_comision_id']['titulo'] = 'Id';
        $columns['inm_conf_comision_descripcion']['titulo'] = 'Descripcion';
        $columns['inm_tipo_comision_descripcion']['titulo'] = 'Tipo de Comision';
        $columns['com_tipo_agente_descripcion']['titulo'] = 'Tipo de Agente';
        $columns['inm_conf_comision_porcentaje_pago']['titulo'] = 'Porcentaje';
        $columns['inm_conf_comision_monto_pago_fijo']['titulo'] = 'Monto Fijo';
        $columns['inm_conf_comision_predeterminado']['titulo'] = 'Predeterminada';

        $filtro = array(
            'inm_conf_comision.id',
            'inm_conf_comision.descripcion',
            'inm_tipo_comision.descripcion',
            'com_tipo_agente.descripcion',
            'inm_conf_comision.porcentaje_pago',
            'inm_conf_comision.monto_pago_fijo'
        );

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;
        return $datatables;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        foreach (array(
            'descripcion' => 'Descripcion',
            'porcentaje_pago' => 'Porcentaje de Pago',
            'monto_pago_fijo' => 'Monto de Pago Fijo',
            'predeterminado' => 'Predeterminada'
        ) as $key => $label) {
            $keys_selects = (new init())->key_select_txt(
                cols: 6,
                key: $key,
                keys_selects: $keys_selects,
                place_holder: $label,
                required: in_array($key, array('porcentaje_pago', 'monto_pago_fijo'), true) ? false : true
            );
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al maquetar ' . $key, data: $keys_selects);
            }
        }

        return $keys_selects;
    }
}
