<?php
use config\views;

echo $controlador->inputs->inm_comision_id;
echo $controlador->inputs->inm_ubicacion_id;
include (new views())->ruta_templates . 'botons/submit/alta_bd.php';
