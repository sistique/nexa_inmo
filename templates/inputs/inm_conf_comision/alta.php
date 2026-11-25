<?php /** @var gamboamartin\inmuebles\controllers\controlador_inm_conf_comision $controlador */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->inm_tipo_comision_id; ?>
<?php echo $controlador->inputs->com_tipo_agente_id; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php echo $controlador->inputs->porcentaje_pago; ?>
<?php echo $controlador->inputs->monto_pago_fijo; ?>
<?php echo $controlador->inputs->fecha_inicial; ?>
<?php echo $controlador->inputs->fecha_final; ?>
<?php echo $controlador->inputs->predeterminado; ?>
<?php include (new views())->ruta_templates . 'botons/submit/alta_bd.php'; ?>
