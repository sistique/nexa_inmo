<?php

namespace gamboamartin\inmuebles\models;

use PDO;

class inm_rel_agente_empleado extends _rel_agente
{
    public function __construct(PDO $link)
    {
        parent::__construct(link: $link, tabla: 'inm_rel_agente_empleado', entidad: 'em_empleado',
            etiqueta: 'Agente empleado');
    }
}
