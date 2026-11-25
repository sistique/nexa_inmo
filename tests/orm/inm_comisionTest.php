<?php

namespace gamboamartin\inmuebles\tests\orm;

use gamboamartin\base_modelos\base_modelos;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_comision;
use gamboamartin\inmuebles\models\inm_rel_comision_comprador;
use gamboamartin\inmuebles\models\inm_rel_comision_ubicacion;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class inm_comisionTest extends TestCase
{
    protected function setUp(): void
    {
        errores::$error = false;
    }

    protected function tearDown(): void
    {
        errores::$error = false;
    }

    private function modelo(string $clase): inm_comision|inm_rel_comision_comprador|inm_rel_comision_ubicacion
    {
        $reflection = new ReflectionClass($clase);
        $modelo = $reflection->newInstanceWithoutConstructor();
        $modelo->error = new errores();
        $modelo->validacion = new base_modelos();
        if ($clase !== inm_comision::class) {
            $campo = $clase === inm_rel_comision_ubicacion::class ? 'inm_ubicacion_id' : 'inm_comprador_id';
            $reflection->getProperty('campo_relacion')->setValue($modelo, $campo);
        }
        return $modelo;
    }

    public function test_rechaza_identificadores_e_importes_invalidos(): void
    {
        $modelo = $this->modelo(clase: inm_comision::class);
        $registro = array('descripcion' => 'Comision', 'em_empleado_id' => 1,
            'inm_periodo_comision_id' => 1, 'inm_conf_comision_id' => 1, 'inm_status_comision_id' => 1,
            'base_comision' => '100.00', 'monto_pago' => '10.00');
        foreach (array('em_empleado_id' => 0, 'inm_periodo_comision_id' => 0, 'inm_conf_comision_id' => 0,
                       'inm_status_comision_id' => 0, 'base_comision' => '-1', 'monto_pago' => 'texto') as $key => $valor) {
            errores::$error = false;
            $modelo->registro = array_replace($registro, array($key => $valor));
            $this->assertIsArray($modelo->alta_bd());
            $this->assertTrue(errores::$error, $key);

            errores::$error = false;
            $this->assertIsArray($modelo->modifica_bd(registro: array($key => $valor), id: 1));
            $this->assertTrue(errores::$error, $key);
        }
    }

    public function test_relaciones_validan_ids_y_generan_descripcion(): void
    {
        $sesion = $_SESSION ?? null;
        unset($_SESSION['usuario_id']);
        try {
            foreach (array(inm_rel_comision_ubicacion::class => 'inm_ubicacion_id',
                           inm_rel_comision_comprador::class => 'inm_comprador_id') as $clase => $campo) {
                errores::$error = false;
                $modelo = $this->modelo(clase: $clase);
                foreach (array('inm_comision_id', $campo) as $invalido) {
                    errores::$error = false;
                    $registro = array('inm_comision_id' => 12, $campo => 34);
                    $registro[$invalido] = 0;
                    $modelo->registro = $registro;
                    $this->assertIsArray($modelo->alta_bd());
                    $this->assertTrue(errores::$error, $invalido);
                }

                errores::$error = false;
                $modelo->registro = array('inm_comision_id' => 12, $campo => 34);
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
