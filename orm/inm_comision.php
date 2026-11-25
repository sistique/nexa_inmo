<?php

namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use PDO;
use stdClass;

class inm_comision extends _modelo_comision
{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_comision';
        $columnas = array(
            $tabla => false,
            'em_empleado' => $tabla,
            'inm_periodo_comision' => $tabla,
            'inm_conf_comision' => $tabla,
            'inm_status_comision' => $tabla
        );
        $campos = array(
            'em_empleado_id', 'inm_periodo_comision_id', 'inm_conf_comision_id',
            'inm_status_comision_id', 'base_comision', 'monto_pago'
        );

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos, columnas: $columnas,
            atributos_criticos: $campos);
        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Comisiones';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $valida = $this->valida_campos_comision(registro: $this->registro, insercion: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar comision', data: $valida);
        }

        $resultado = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar comision', data: $resultado);
        }
        return $resultado;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $valida = $this->valida_campos_comision(registro: $registro, insercion: false);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar comision', data: $valida);
        }

        $resultado = parent::modifica_bd(registro: $registro, id: $id, reactiva: $reactiva,
            keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar comision', data: $resultado);
        }
        return $resultado;
    }

    private function valida_campos_comision(array $registro, bool $insercion): array|bool
    {
        $ids = array('em_empleado_id', 'inm_periodo_comision_id', 'inm_conf_comision_id', 'inm_status_comision_id');
        $montos = array('base_comision', 'monto_pago');
        if (!$insercion) {
            $ids = array_values(array_filter($ids, static fn(string $key): bool => array_key_exists($key, $registro)));
            $montos = array_values(array_filter($montos, static fn(string $key): bool => array_key_exists($key, $registro)));
        }

        if ($ids !== array()) {
            $valida = $this->validacion->valida_ids(keys: $ids, registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar relaciones', data: $valida);
            }
        }
        if ($montos !== array()) {
            $valida = $this->validacion->valida_double_mayores_igual_0(keys: $montos, registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar importes', data: $valida);
            }
        }
        return true;
    }
}
