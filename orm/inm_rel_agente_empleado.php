<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class inm_rel_agente_empleado extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_rel_agente_empleado';
        $columnas = array($tabla => false, 'com_agente' => $tabla, 'em_empleado' => $tabla);
        $campos_obligatorios = array('com_agente_id', 'em_empleado_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, atributos_criticos: $campos_obligatorios);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Agente empleado';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $valida = $this->validacion->valida_ids(keys: array('com_agente_id', 'em_empleado_id'),
            registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar relacion', data: $valida);
        }

        if (!isset($this->registro['descripcion'])) {
            $this->registro['descripcion'] = $this->registro['com_agente_id'].' '.$this->registro['em_empleado_id'];
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
        if (array_key_exists('com_agente_id', $registro) || array_key_exists('em_empleado_id', $registro)) {
            if (!isset($registro['com_agente_id']) || !isset($registro['em_empleado_id'])) {
                $anterior = $this->registro(registro_id: $id, columnas_en_bruto: true, retorno_obj: true);
                if (errores::$error) {
                    return $this->error->error(mensaje: 'Error al obtener relacion', data: $anterior);
                }
                $registro += array('com_agente_id' => $anterior->com_agente_id,
                    'em_empleado_id' => $anterior->em_empleado_id);
            }

            $valida = $this->validacion->valida_ids(keys: array('com_agente_id', 'em_empleado_id'),
                registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar relacion', data: $valida);
            }
            $registro['descripcion'] = $registro['com_agente_id'].' '.$registro['em_empleado_id'];
            $registro['codigo'] = $registro['descripcion'];
        }

        $resultado = parent::modifica_bd(registro: $registro, id: $id, reactiva: $reactiva,
            keys_integra_ds: $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar relacion', data: $resultado);
        }
        return $resultado;
    }
}
