<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class inm_periodo_comision extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_periodo_comision';
        $columnas = array(
            $tabla => false,
            'inm_conf_comision' => $tabla,
            'inm_status_periodo_comision' => $tabla
        );
        $campos_obligatorios = array(
            'fecha_inicial',
            'fecha_final',
            'fecha_pago',
            'inm_conf_comision_id',
            'inm_status_periodo_comision_id'
        );
        $atributos_criticos = $campos_obligatorios;

        parent::__construct(
            link: $link,
            tabla: $tabla,
            campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,
            atributos_criticos: $atributos_criticos
        );

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Periodo de Comision';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $keys = array('fecha_inicial', 'fecha_final', 'fecha_pago');
        $valida = $this->validacion->fechas_in_array(data: $this->registro, keys: $keys);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar fechas del periodo', data: $valida);
        }

        $this->registro = $this->integra_descripcion_periodo(registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar descripcion del periodo', data: $this->registro);
        }

        return parent::alta_bd(keys_integra_ds: $keys_integra_ds);
    }

    public function modifica_bd(
        array $registro,
        int $id,
        bool $reactiva = false,
        array $keys_integra_ds = array('codigo', 'descripcion')
    ): array|stdClass {
        $keys = array('fecha_inicial', 'fecha_final', 'fecha_pago');
        $valida = $this->validacion->fechas_in_array(data: $registro, keys: $keys);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar fechas del periodo', data: $valida);
        }

        $registro = $this->integra_descripcion_periodo(registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar descripcion del periodo', data: $registro);
        }

        return parent::modifica_bd(
            registro: $registro,
            id: $id,
            reactiva: $reactiva,
            keys_integra_ds: $keys_integra_ds
        );
    }

    private function integra_descripcion_periodo(array $registro): array
    {
        $keys = array('fecha_inicial', 'fecha_final');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar fechas del periodo', data: $valida);
        }

        $descripcion = 'Periodo de comision ' . $registro['fecha_inicial'] . ' al ' . $registro['fecha_final'];
        $registro['descripcion'] = $descripcion;
        if (!isset($registro['codigo'])) {
            $registro['codigo'] = 'PC-' . ($registro['inm_conf_comision_id'] ?? '0') . '-';
            $registro['codigo'] .= $registro['fecha_inicial'] . '-' . $registro['fecha_final'];
        }

        return $registro;
    }
}
