<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use stdClass;

class _modelo_comision extends _modelo_parent
{
    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $fecha = date('Y-m-d H:i:s');
        $this->registro['fecha_alta'] = $fecha;
        $this->registro['fecha_update'] = $fecha;

        $resultado = parent::alta_bd(keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar registro de comision', data: $resultado);
        }
        return $resultado;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro['fecha_update'] = date('Y-m-d H:i:s');
        $resultado = parent::modifica_bd(registro: $registro, id: $id, reactiva: $reactiva,
            keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar registro de comision', data: $resultado);
        }
        return $resultado;
    }
}
