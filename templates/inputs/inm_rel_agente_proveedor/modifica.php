<?php
use config\views;

echo $controlador->inputs->com_agente_id;
echo $controlador->inputs->gt_proveedor_id;
include (new views())->ruta_templates.'botons/submit/modifica_bd.php';
