<?php

namespace gamboamartin\inmuebles\models;

use PDO;

class inm_rel_agente_proveedor extends _rel_agente
{
    public function __construct(PDO $link)
    {
        parent::__construct(link: $link, tabla: 'inm_rel_agente_proveedor', entidad: 'gt_proveedor',
            etiqueta: 'Agente proveedor');
    }
}
