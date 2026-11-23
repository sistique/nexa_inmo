<?php

namespace gamboamartin\inmuebles\tests\orm;

use gamboamartin\base_modelos\base_modelos;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\_rel_agente;
use gamboamartin\inmuebles\models\inm_rel_agente_empleado;
use gamboamartin\inmuebles\models\inm_rel_agente_proveedor;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

class inm_rel_agenteTest extends TestCase
{
    protected function setUp(): void
    {
        errores::$error = false;
    }

    protected function tearDown(): void
    {
        errores::$error = false;
    }

    private function modelo(string $clase, string $entidad_id): _rel_agente
    {
        errores::$error = false;
        $modelo = (new ReflectionClass($clase))->newInstanceWithoutConstructor();
        $modelo->error = new errores();
        $modelo->validacion = new base_modelos();
        (new ReflectionProperty(_rel_agente::class, 'entidad_id'))->setValue($modelo, $entidad_id);
        return $modelo;
    }

    public function test_valida_identificadores_al_crear_y_modificar(): void
    {
        foreach (array(inm_rel_agente_proveedor::class => 'gt_proveedor_id',
                       inm_rel_agente_empleado::class => 'em_empleado_id') as $clase => $entidad_id) {
            $modelo = $this->modelo(clase: $clase, entidad_id: $entidad_id);
            foreach (array(array('com_agente_id' => 0, $entidad_id => 1),
                           array('com_agente_id' => 1, $entidad_id => -1)) as $registro) {
                errores::$error = false;
                $modelo->registro = $registro;
                $this->assertIsArray($modelo->alta_bd());
                $this->assertTrue(errores::$error);

                errores::$error = false;
                $this->assertIsArray($modelo->modifica_bd(registro: $registro, id: 1));
                $this->assertTrue(errores::$error);
            }
        }
    }

    public function test_genera_descripcion_desde_los_identificadores(): void
    {
        $sesion = $_SESSION ?? null;
        unset($_SESSION['usuario_id']);
        try {
            foreach (array(inm_rel_agente_proveedor::class => 'gt_proveedor_id',
                           inm_rel_agente_empleado::class => 'em_empleado_id') as $clase => $entidad_id) {
                errores::$error = false;
                $modelo = $this->modelo(clase: $clase, entidad_id: $entidad_id);
                $modelo->registro = array('com_agente_id' => 12, $entidad_id => 34);
                $modelo->alta_bd();
                $this->assertSame('12 34', $modelo->registro['descripcion']);
            }
        } finally {
            if ($sesion === null) {
                unset($_SESSION);
            } else {
                $_SESSION = $sesion;
            }
        }
    }
}
