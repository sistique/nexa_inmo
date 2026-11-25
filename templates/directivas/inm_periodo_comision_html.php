<?php

namespace gamboamartin\inmuebles\html;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_periodo_comision;
use gamboamartin\system\html_controler;
use PDO;

class inm_periodo_comision_html extends html_controler
{
    public function select_inm_periodo_comision_id(
        int $cols,
        bool $con_registros,
        int $id_selected,
        PDO $link,
        bool $disabled = false,
        array $filtro = array()
    ): array|string {
        $modelo = new inm_periodo_comision(link: $link);
        $select = $this->select_catalogo(
            cols: $cols,
            con_registros: $con_registros,
            id_selected: $id_selected,
            modelo: $modelo,
            columns_ds: array('inm_periodo_comision_descripcion_select'),
            disabled: $disabled,
            filtro: $filtro,
            label: 'Periodo de Comision',
            required: true
        );
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }

        return $select;
    }
}
