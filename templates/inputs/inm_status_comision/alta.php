<?php
use config\views;

echo $controlador->inputs->descripcion;
include (new views())->ruta_templates . 'botons/submit/alta_bd.php';
