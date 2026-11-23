<?php
use config\views;

echo $controlador->inputs->com_agente_id;
echo $controlador->inputs->em_empleado_id;
include (new views())->ruta_templates.'botons/submit/modifica_bd.php';
