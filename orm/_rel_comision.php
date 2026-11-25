<?php

namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use stdClass;

class _rel_comision extends _modelo_comision
{
    protected string $campo_relacion;

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $valida = $this->validacion->valida_ids(
            keys: array('inm_comision_id', $this->campo_relacion), registro: $this->registro
        );
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar relacion', data: $valida);
        }
        if (!isset($this->registro['descripcion'])) {
            $this->registro['descripcion'] = $this->registro['inm_comision_id'] . ' ' .
                $this->registro[$this->campo_relacion];
        }

        $resultado = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar relacion', data: $resultado);
        }
        return $resultado;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if (array_key_exists('inm_comision_id', $registro) || array_key_exists($this->campo_relacion, $registro)) {
            if (!array_key_exists('inm_comision_id', $registro) || !array_key_exists($this->campo_relacion, $registro)) {
                $anterior = $this->registro(registro_id: $id, columnas_en_bruto: true, retorno_obj: true);
                if (errores::$error) {
                    return $this->error->error(mensaje: 'Error al obtener relacion', data: $anterior);
                }
                $registro += array('inm_comision_id' => $anterior->inm_comision_id,
                    $this->campo_relacion => $anterior->{$this->campo_relacion});
            }
            $valida = $this->validacion->valida_ids(
                keys: array('inm_comision_id', $this->campo_relacion), registro: $registro
            );
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar relacion', data: $valida);
            }
            if (!isset($registro['descripcion'])) {
                $registro['descripcion'] = $registro['inm_comision_id'] . ' ' . $registro[$this->campo_relacion];
            }
        }

        $resultado = parent::modifica_bd(registro: $registro, id: $id, reactiva: $reactiva,
            keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar relacion', data: $resultado);
        }
        return $resultado;
    }
}
