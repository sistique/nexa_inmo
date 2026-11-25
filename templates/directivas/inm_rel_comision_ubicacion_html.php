<?php

namespace gamboamartin\inmuebles\html;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_rel_comision_ubicacion;
use gamboamartin\system\html_controler;
use PDO;

class inm_rel_comision_ubicacion_html extends html_controler
{
    public function select_inm_rel_comision_ubicacion_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                                         bool $disabled = false, array $filtro = array()): array|string
    {
        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: new inm_rel_comision_ubicacion(link: $link), disabled: $disabled, filtro: $filtro,
            label: 'Comision y Ubicacion', required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }
}
