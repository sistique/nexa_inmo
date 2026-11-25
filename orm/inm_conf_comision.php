<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class inm_conf_comision extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_conf_comision';
        $columnas = array(
            $tabla => false,
            'com_tipo_agente' => $tabla,
            'inm_tipo_comision' => $tabla
        );

        parent::__construct(link: $link, tabla: $tabla, columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Configuracion de Comision';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $this->registro = $this->normaliza_opcionales(registro: $this->registro, insercion: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al preparar configuracion', data: $this->registro);
        }

        return parent::alta_bd(keys_integra_ds: $keys_integra_ds);
    }

    public function modifica_bd(
        array $registro,
        int $id,
        bool $reactiva = false,
        array $keys_integra_ds = array('codigo', 'descripcion')
    ): array|stdClass {
        $registro = $this->normaliza_opcionales(registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al preparar configuracion', data: $registro);
        }

        return parent::modifica_bd(
            registro: $registro,
            id: $id,
            reactiva: $reactiva,
            keys_integra_ds: $keys_integra_ds
        );
    }

    private function normaliza_opcionales(array $registro, bool $insercion = false): array
    {
        foreach (array(
            'fecha_inicial',
            'fecha_final',
            'porcentaje_pago',
            'monto_pago_fijo',
            'com_tipo_agente_id',
            'inm_tipo_comision_id'
        ) as $campo) {
            if (!array_key_exists($campo, $registro)) {
                if ($insercion) {
                    $registro[$campo] = null;
                }
                continue;
            }
            if ($registro[$campo] === null || trim((string)$registro[$campo]) === '') {
                $registro[$campo] = null;
            }
        }

        if ($insercion && !isset($registro['predeterminado'])) {
            $registro['predeterminado'] = 'inactivo';
        }

        return $registro;
    }
}
