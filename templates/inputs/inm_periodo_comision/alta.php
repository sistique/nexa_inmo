<?php /** @var gamboamartin\inmuebles\controllers\controlador_inm_periodo_comision $controlador */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->inm_conf_comision_id; ?>
<?php echo $controlador->inputs->inm_status_periodo_comision_id; ?>
<?php echo $controlador->inputs->fecha_inicial; ?>
<?php echo $controlador->inputs->fecha_final; ?>
<?php echo $controlador->inputs->fecha_pago; ?>
<?php include (new views())->ruta_templates . 'botons/submit/alta_bd.php'; ?>
