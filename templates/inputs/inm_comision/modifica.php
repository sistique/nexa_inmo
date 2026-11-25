<?php
use config\views;

echo $controlador->inputs->descripcion;
echo $controlador->inputs->em_empleado_id;
echo $controlador->inputs->inm_periodo_comision_id;
echo $controlador->inputs->inm_conf_comision_id;
echo $controlador->inputs->inm_status_comision_id;
echo $controlador->inputs->base_comision;
echo $controlador->inputs->monto_pago;
include (new views())->ruta_templates . 'botons/submit/modifica_bd.php';
