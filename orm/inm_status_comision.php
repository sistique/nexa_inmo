<?php

namespace gamboamartin\inmuebles\models;

use PDO;

class inm_status_comision extends _modelo_comision
{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_status_comision';
        parent::__construct(link: $link, tabla: $tabla, columnas: array($tabla => false));
        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Estatus de Comision';
    }
}
