<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _rel_agente extends _modelo_parent
{
    private string $entidad_id;

    public function __construct(PDO $link, string $tabla, string $entidad, string $etiqueta)
    {
        $this->entidad_id = $entidad.'_id';
        $columnas = array($tabla => false, 'com_agente' => $tabla, $entidad => $tabla);
        $campos = array('com_agente_id', $this->entidad_id);

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos, columnas: $columnas,
            atributos_criticos: $campos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = $etiqueta;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $valida = $this->validacion->valida_ids(keys: array('com_agente_id', $this->entidad_id),
            registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar relacion', data: $valida);
        }

        if (!isset($this->registro['descripcion'])) {
            $this->registro['descripcion'] = $this->registro['com_agente_id'].' '.$this->registro[$this->entidad_id];
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
        if (array_key_exists('com_agente_id', $registro) || array_key_exists($this->entidad_id, $registro)) {
            if (!isset($registro['com_agente_id']) || !isset($registro[$this->entidad_id])) {
                $anterior = $this->registro(registro_id: $id, columnas_en_bruto: true, retorno_obj: true);
                if (errores::$error) {
                    return $this->error->error(mensaje: 'Error al obtener relacion', data: $anterior);
                }
                $registro += array('com_agente_id' => $anterior->com_agente_id,
                    $this->entidad_id => $anterior->{$this->entidad_id});
            }

            $valida = $this->validacion->valida_ids(keys: array('com_agente_id', $this->entidad_id),
                registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar relacion', data: $valida);
            }
            $registro['descripcion'] = $registro['com_agente_id'].' '.$registro[$this->entidad_id];
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
