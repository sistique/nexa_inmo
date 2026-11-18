<?php

use PHPUnit\Framework\TestCase;

final class ProcesoProspectoTest extends TestCase
{
    public function testBotonesRetornanAlApartadoDelProceso(): void
    {
        $vacio = new class {
            public function __get(string $name): self
            {
                return $this;
            }

            public function __toString(): string
            {
                return '';
            }
        };

        $controlador = new class($vacio) {
            public array $acciones_visibles_permitidas = array();
            public array $beneficiarios = array();
            public array $referencias = array();
            public array $inm_conf_docs_prospecto = array();
            public array $relaciones = array();
            public array $etapas = array();
            public bool $es_agente = false;
            public bool $ver_descripcion = false;
            public string $link_modifica_bd = 'modifica_bd';
            public object $header_frontend;
            public object $inputs;

            public function __construct(private object $vacio)
            {
                $this->header_frontend = $vacio;
                $this->inputs = new class($vacio) {
                    public string $btn_action_next = '<input type="hidden" name="btn_action_next" value="proceso_prospecto">';

                    public function __construct(private object $vacio) {}

                    public function __get(string $name): object
                    {
                        return $this->vacio;
                    }
                };
            }

            public function __get(string $name): string
            {
                return '';
            }
        };

        ob_start();
        include __DIR__ . '/../../views/inm_prospecto/proceso_prospecto.php';
        $html = ob_get_clean();

        $documento = new DOMDocument();
        $anterior = libxml_use_internal_errors(true);
        $documento->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($anterior);
        $xpath = new DOMXPath($documento);

        $formulario = $xpath->query('//form[@action="modifica_bd" and @method="post"]');
        self::assertCount(1, $formulario);
        self::assertCount(1, $xpath->query('.//input[@name="btn_action_next" and @value="proceso_prospecto"]', $formulario->item(0)));

        for ($i = 1; $i <= 12; $i++) {
            $botones = $xpath->query(
                ".//div[@id='apartado_$i']//button[@type='submit' and @name='apartado_retorno' and @value='apartado_$i']",
                $formulario->item(0)
            );
            self::assertCount(1, $botones, "El apartado $i debe enviar su propia ancla");
        }
        self::assertCount(12, $xpath->query('.//button[@name="apartado_retorno"]', $formulario->item(0)));
    }
}
