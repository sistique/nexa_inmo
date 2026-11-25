<?php

namespace gamboamartin\inmuebles\html;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_comision;
use gamboamartin\system\html_controler;
use PDO;

class inm_comision_html extends html_controler
{
    public function select_inm_comision_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                            bool $disabled = false, array $filtro = array()): array|string
    {
        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: new inm_comision(link: $link), disabled: $disabled, filtro: $filtro,
            label: 'Comision', required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }
}
