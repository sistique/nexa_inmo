<?php

namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_periodo_comision_html;
use gamboamartin\inmuebles\models\inm_periodo_comision;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_periodo_comision extends _ctl_base
{
    public function __construct(
        PDO $link,
        html $html = new \gamboamartin\template_1\html(),
        stdClass $paths_conf = new stdClass()
    ) {
        $modelo = new inm_periodo_comision(link: $link);
        $html_ = new inm_periodo_comision_html(html: $html);
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

        $this->row_upd->fecha_inicial = date('Y-m-d');
        $this->row_upd->fecha_final = date('Y-m-d');
        $this->row_upd->fecha_pago = date('Y-m-d');
        $keys_selects = $this->keys_selects(id_conf_comision: -1, id_status: -1);
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

        foreach (array(
            'fecha_inicial' => 'Fecha Inicial',
            'fecha_final' => 'Fecha Final',
            'fecha_pago' => 'Fecha de Pago'
        ) as $campo => $label) {
            $this->integra_input_fecha(campo: $campo, label: $label);
            if (errores::$error) {
                return $this->retorno_error(
                    mensaje: 'Error al generar ' . $label,
                    data: $this->inputs,
                    header: $header,
                    ws: $ws
                );
            }
        }

        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array();
        $keys->selects = array();
        $init_data = array(
            'inm_conf_comision' => 'gamboamartin\\inmuebles',
            'inm_status_periodo_comision' => 'gamboamartin\\inmuebles'
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
            id_conf_comision: (int)$this->row_upd->inm_conf_comision_id,
            id_status: (int)$this->row_upd->inm_status_periodo_comision_id
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

        foreach (array(
            'fecha_inicial' => 'Fecha Inicial',
            'fecha_final' => 'Fecha Final',
            'fecha_pago' => 'Fecha de Pago'
        ) as $campo => $label) {
            $this->integra_input_fecha(campo: $campo, label: $label);
            if (errores::$error) {
                return $this->retorno_error(
                    mensaje: 'Error al generar ' . $label,
                    data: $this->inputs,
                    header: $header,
                    ws: $ws
                );
            }
        }

        return $r_modifica;
    }

    private function keys_selects(int $id_conf_comision, int $id_status): array
    {
        $keys_selects = $this->key_select(
            cols: 6,
            con_registros: true,
            filtro: array(),
            key: 'inm_conf_comision_id',
            keys_selects: array(),
            id_selected: $id_conf_comision,
            label: 'Configuracion de Comision'
        );
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar configuracion', data: $keys_selects);
        }

        $keys_selects = $this->key_select(
            cols: 6,
            con_registros: true,
            filtro: array(),
            key: 'inm_status_periodo_comision_id',
            keys_selects: $keys_selects,
            id_selected: $id_status,
            label: 'Estatus del Periodo'
        );
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar estatus', data: $keys_selects);
        }

        return $keys_selects;
    }

    private function integra_input_fecha(string $campo, string $label): void
    {
        $fecha = $this->html->input_fecha(
            cols: 4,
            row_upd: $this->row_upd,
            value_vacio: false,
            name: $campo,
            place_holder: $label,
            value: $this->row_upd->$campo
        );
        if (errores::$error) {
            $this->errores->error(mensaje: 'Error al generar input fecha', data: $fecha);
            return;
        }

        $this->inputs->$campo = $fecha;
    }

    private function init_datatable(): stdClass
    {
        $columns['inm_periodo_comision_id']['titulo'] = 'Id';
        $columns['inm_periodo_comision_fecha_inicial']['titulo'] = 'Fecha Inicial';
        $columns['inm_periodo_comision_fecha_final']['titulo'] = 'Fecha Final';
        $columns['inm_periodo_comision_fecha_pago']['titulo'] = 'Fecha de Pago';
        $columns['inm_conf_comision_descripcion']['titulo'] = 'Configuracion';
        $columns['inm_status_periodo_comision_descripcion']['titulo'] = 'Estatus';
        $filtro = array(
            'inm_periodo_comision.id',
            'inm_periodo_comision.fecha_inicial',
            'inm_periodo_comision.fecha_final',
            'inm_periodo_comision.fecha_pago',
            'inm_conf_comision.descripcion',
            'inm_status_periodo_comision.descripcion'
        );

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;
        return $datatables;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        return $keys_selects;
    }
}
