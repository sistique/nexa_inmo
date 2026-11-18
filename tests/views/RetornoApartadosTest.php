<?php

use gamboamartin\inmuebles\controllers\_retorno_modifica;
use PHPUnit\Framework\TestCase;

final class RetornoApartadosTest extends TestCase
{
    public function testFormulariosEnvianSuApartado(): void
    {
        $casos = array(
            array('views/inm_prospecto_ubicacion/modifica.php', 9, null, false),
            array('views/inm_prospecto_ubicacion/proceso_prospecto_ubicacion.php', 9, 'proceso_prospecto_ubicacion', false),
            array('views/inm_ubicacion/modifica.php', 9, null, false),
            array('views/inm_ubicacion/proceso_ubicacion.php', 9, 'proceso_ubicacion', true),
            array('templates/inputs/inm_comprador/modifica.php', 12, null, false),
            array('views/inm_comprador/proceso_cliente.php', 12, 'proceso_cliente', true),
        );

        foreach ($casos as [$archivo, $total, $proceso, $etapa]) {
            $contenido = file_get_contents(__DIR__ . '/../../' . $archivo);
            self::assertStringContainsString('js/retorno_apartado.js', $contenido, $archivo);

            if ($archivo === 'templates/inputs/inm_comprador/modifica.php') {
                $formularios = array($contenido);
            } else {
                preg_match_all(
                    '/<form method="post" action="<\?php echo \$controlador->link_modifica_bd; \?>"[^>]*>.*?<\/form>/s',
                    $contenido,
                    $coincidencias
                );
                $formularios = $coincidencias[0];
            }

            self::assertCount($etapa ? 2 : 1, $formularios, $archivo);
            foreach ($formularios as $indice => $formulario) {
                if ($proceso !== null) {
                    self::assertStringContainsString('$controlador->inputs->btn_action_next', $formulario, $archivo);
                }
                if ($indice === 1) {
                    self::assertStringContainsString('name="params[pestana_general_actual]" value="pestanageneral2"', $formulario);
                }

                $html = preg_replace('/<\?php.*?\?>/s', '', $formulario);
                $documento = new DOMDocument();
                $errores_previos = libxml_use_internal_errors(true);
                $documento->loadHTML($html);
                libxml_clear_errors();
                libxml_use_internal_errors($errores_previos);
                $xpath = new DOMXPath($documento);
                $prefijo = $indice === 1 ? 'apartado_etapa_' : 'apartado_';
                for ($i = 1; $i <= $total; $i++) {
                    $id = $prefijo . $i;
                    self::assertCount(1, $xpath->query(
                        "//div[@id='$id']//button[@type='submit' and @name='apartado_retorno' and @value='$id']"
                    ), "$archivo: $id");
                }
                self::assertCount($total, $xpath->query('//button[@name="apartado_retorno"]'), $archivo);
            }
        }
    }

    public function testCapturaRetornoAntesDeQueElPadreLimpieElPost(): void
    {
        $post_anterior = $_POST;
        try {
            $_POST = array(
                'apartado_retorno' => 'apartado_etapa_3',
                'btn_action_next' => 'proceso_cliente',
                'params' => array('pestana_general_actual' => 'pestanageneral2'),
                'nombre' => 'Nombre',
            );
            $retorno = (new _retorno_modifica())->captura(registro_id: 7, tabla: 'inm_comprador');
            self::assertSame('apartado_etapa_3', $retorno->ancla);
            self::assertSame('apartado_etapa_3', $_POST['ancla']);
            self::assertArrayNotHasKey('apartado_retorno', $_POST);
            self::assertSame('proceso_cliente', $retorno->siguiente_view);
            self::assertSame('inm_comprador', $retorno->seccion);
            self::assertSame(7, $retorno->registro_id);
            self::assertSame(array('pestana_general_actual' => 'pestanageneral2'), $retorno->params);
            self::assertSame('Nombre', $_POST['nombre']);

            $_POST = array('nombre' => 'Nombre');
            $retorno = (new _retorno_modifica())->captura(registro_id: 7, tabla: 'inm_ubicacion');
            self::assertSame('modifica', $retorno->siguiente_view);
            self::assertSame('', $retorno->ancla);
            self::assertArrayNotHasKey('ancla', $_POST);
        } finally {
            $_POST = $post_anterior;
        }
    }
}
