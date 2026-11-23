<?php

namespace gamboamartin\inmuebles\controllers;

use gamboamartin\inmuebles\models\inm_rel_agente_proveedor;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_rel_agente_proveedor extends _rel_agente
{
    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        parent::__construct(link: $link, html: $html, paths_conf: $paths_conf,
            modelo: new inm_rel_agente_proveedor(link: $link), entidad: 'gt_proveedor',
            namespace_entidad: 'gamboamartin\\gastos', label_entidad: 'Proveedor', campo_lista: 'razon_social');
    }
}
