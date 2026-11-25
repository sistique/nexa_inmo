<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;

class inm_tipo_comision extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_tipo_comision';
        $columnas = array($tabla => false);

        parent::__construct(link: $link, tabla: $tabla, columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Tipos de Comision';
    }
}
