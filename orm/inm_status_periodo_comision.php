<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;

class inm_status_periodo_comision extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_status_periodo_comision';
        $columnas = array($tabla => false);

        parent::__construct(link: $link, tabla: $tabla, columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Estatus de Periodo de Comision';
    }
}
