<?php

namespace gamboamartin\inmuebles\controllers;

use gamboamartin\inmuebles\html\inm_rel_comision_comprador_html;
use gamboamartin\inmuebles\models\inm_rel_comision_comprador;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_rel_comision_comprador extends _ctl_rel_comision
{
    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $this->entidad_relacion = 'inm_comprador';
        $this->label_relacion = 'Comprador';
        $datatables = new stdClass();
        $datatables->columns = array(
            'inm_rel_comision_comprador_id' => array('titulo' => 'Id'),
            'inm_comision_descripcion' => array('titulo' => 'Comision'),
            'inm_comprador_descripcion' => array('titulo' => 'Comprador')
        );
        $datatables->filtro = array('inm_rel_comision_comprador.id', 'inm_comision.descripcion',
            'inm_comprador.descripcion');

        parent::__construct(html: new inm_rel_comision_comprador_html(html: $html), link: $link,
            modelo: new inm_rel_comision_comprador(link: $link),
            obj_link: new links_menu(link: $link, registro_id: $this->registro_id),
            datatables: $datatables, paths_conf: $paths_conf);
        $this->lista_get_data = true;
    }
}
