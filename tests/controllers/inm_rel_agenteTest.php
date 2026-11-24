<?php

namespace gamboamartin\inmuebles\tests\controllers;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_rel_agente_empleado;
use gamboamartin\inmuebles\controllers\controlador_inm_rel_agente_proveedor;
use gamboamartin\inmuebles\html\inm_rel_agente_empleado_html;
use gamboamartin\inmuebles\html\inm_rel_agente_proveedor_html;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use stdClass;

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

    public function test_selects_de_alta_y_modifica(): void
    {
        foreach (array(
            controlador_inm_rel_agente_proveedor::class => array('gt_proveedor', 'Proveedor'),
            controlador_inm_rel_agente_empleado::class => array('em_empleado', 'Empleado')
        ) as $clase => [$entidad, $label]) {
            $controlador = (new ReflectionClass($clase))->newInstanceWithoutConstructor();
            $controlador->errores = new errores();
            $controlador->row_upd = (object) array('com_agente_id' => 12, $entidad.'_id' => 34);
            $metodo = new ReflectionMethod($clase, 'selects');

            $alta = $metodo->invoke($controlador, false);
            $this->assertFalse(errores::$error);
            $this->assertSame('-1', $alta['com_agente_id']->id_selected);
            $this->assertSame('-1', $alta[$entidad.'_id']->id_selected);
            $this->assertTrue($alta['com_agente_id']->required);
            $this->assertTrue($alta[$entidad.'_id']->required);

            $modifica = $metodo->invoke($controlador, true);
            $this->assertFalse(errores::$error);
            $this->assertSame('12', $modifica['com_agente_id']->id_selected);
            $this->assertSame('34', $modifica[$entidad.'_id']->id_selected);
            $this->assertSame($label, $modifica[$entidad.'_id']->label);
        }
    }

    public function test_controladores_y_formularios_con_base_de_datos(): void
    {
        if (getenv('NEXA_TEST_READONLY_DB') !== '1') {
            $this->markTestSkipped('Defina NEXA_TEST_READONLY_DB=1 para probar formularios con la base configurada.');
        }

        $config = new \config\database();
        $link = new \PDO("mysql:host=$config->db_host;dbname=$config->db_name",
            $config->db_user, $config->db_password);
        $get = $_GET;
        $session = $_SESSION ?? null;

        try {
            foreach (array(
                'proveedor' => array(controlador_inm_rel_agente_proveedor::class,
                    inm_rel_agente_proveedor_html::class, 'gt_proveedor_id'),
                'empleado' => array(controlador_inm_rel_agente_empleado::class,
                    inm_rel_agente_empleado_html::class, 'em_empleado_id')
            ) as $tipo => [$clase, $html, $entidad_id]) {
                $_GET = array('seccion' => 'inm_rel_agente_'.$tipo, 'accion' => 'alta', 'session_id' => '1');
                $_SESSION = array('grupo_id' => 1, 'usuario_id' => 2);
                $controlador = new $clase(link: $link, paths_conf: new stdClass());
                $this->assertInstanceOf($html, $controlador->html);
                $this->assertTrue($controlador->lista_get_data);

                $campo_lista = $tipo === 'proveedor' ? 'gt_proveedor.razon_social' : 'em_empleado.descripcion';
                $lista = $controlador->modelo->get_data_lista(order: array($campo_lista => 'ASC'));
                $this->assertFalse(errores::$error, print_r($lista, true));
                $this->assertArrayHasKey('registros', $lista);

                $resultado = $controlador->alta(header: false);
                $this->assertFalse(errores::$error, print_r($resultado, true));
                foreach (array('com_agente_id', $entidad_id) as $key) {
                    $this->assertStringContainsString("name='$key'", $controlador->inputs->$key);
                    $this->assertStringContainsString('required', $controlador->inputs->$key);
                }
            }
        } finally {
            $_GET = $get;
            if ($session === null) {
                unset($_SESSION);
            } else {
                $_SESSION = $session;
            }
        }
    }
}
