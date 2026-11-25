<?php

namespace gamboamartin\inmuebles\models;

use PDO;

class inm_rel_comision_comprador extends _rel_comision
{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_rel_comision_comprador';
        $this->campo_relacion = 'inm_comprador_id';
        $campos = array('inm_comision_id', $this->campo_relacion);
        $columnas = array($tabla => false, 'inm_comision' => $tabla, 'inm_comprador' => $tabla);

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos, columnas: $columnas,
            atributos_criticos: $campos);
        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Comision y Comprador';
    }
}
