<?php

use gamboamartin\inmuebles\models\_expediente;
use gamboamartin\errores\errores;
use PHPUnit\Framework\TestCase;
use setasign\Fpdi\Fpdi;

final class _expedienteTest extends TestCase
{
    public function testOrdenaTiposExistentesYConservaLosNoListados(): void
    {
        $documentos = array(
            array('id' => 1, 'doc_tipo_documento_descripcion' => 'OTRO'),
            array('id' => 2, 'doc_tipo_documento_descripcion' => 'VIGENCIA INE'),
            array('id' => 3, 'doc_tipo_documento_descripcion' => ' ine '),
            array('id' => 4, 'doc_tipo_documento_descripcion' => 'INE'),
            array('id' => 5, 'doc_tipo_documento_descripcion' => 'OTRO')
        );

        $resultado = (new _expediente())->ordena_documentos(
            documentos: $documentos,
            orden_tipos: array('INE', 'INEXISTENTE', 'VIGENCIA INE', 'ine')
        );

        self::assertSame(array(3, 4, 2, 1, 5), array_column($resultado, 'id'));
    }

    public function testOrdenVacioConservaSeleccionYAdmiteListaVacia(): void
    {
        $documentos = array(
            array('id' => 2, 'doc_tipo_documento_descripcion' => 'PREDIAL'),
            array('id' => 1, 'doc_tipo_documento_descripcion' => 'ESCRITURA')
        );
        $expediente = new _expediente();

        self::assertSame($documentos, $expediente->ordena_documentos(documentos: $documentos, orden_tipos: array()));
        self::assertSame(array(), $expediente->ordena_documentos(documentos: array(), orden_tipos: array('INE')));
    }

    public function testComparaDescripcionConAcentosSinDistinguirMayusculas(): void
    {
        $documentos = array(
            array('id' => 1, 'doc_tipo_documento_descripcion' => 'OTRO'),
            array('id' => 2, 'doc_tipo_documento_descripcion' => 'identificación')
        );

        $resultado = (new _expediente())->ordena_documentos(
            documentos: $documentos,
            orden_tipos: array('IDENTIFICACIÓN')
        );

        self::assertSame(array(2, 1), array_column($resultado, 'id'));
    }

    public function testElOrdenSeReflejaEnLasPaginasDelPdf(): void
    {
        $documentos = array();
        $archivos = array();
        foreach (array('OTRO' => 100, 'VIGENCIA INE' => 140, 'INE' => 180) as $tipo => $ancho) {
            $pdf = new FPDF('P', 'mm', array($ancho, 200));
            $pdf->AddPage();
            $ruta = tempnam(sys_get_temp_dir(), 'test pdf ');
            self::assertIsString($ruta);
            file_put_contents($ruta, $pdf->Output('S'));
            $archivos[] = $ruta;
            $documentos[] = array(
                'doc_tipo_documento_descripcion' => $tipo,
                'ruta' => $ruta
            );
        }

        $comprimido = tempnam(sys_get_temp_dir(), 'pdf comprimido ');
        self::assertIsString($comprimido);
        $archivos[] = $comprimido;
        $proceso = proc_open(array('qpdf', '--object-streams=generate', $documentos[1]['ruta'], $comprimido),
            array(0 => array('file', '/dev/null', 'r'), 1 => array('file', '/dev/null', 'w'),
                2 => array('file', '/dev/null', 'w')), $pipes);
        self::assertIsResource($proceso);
        self::assertSame(0, proc_close($proceso));
        $documentos[1]['ruta'] = $comprimido;

        $ordenados = (new _expediente())->ordena_documentos(
            documentos: $documentos,
            orden_tipos: array('INE', 'VIGENCIA INE')
        );
        $salida = tempnam(sys_get_temp_dir(), 'expediente prueba ');
        self::assertIsString($salida);
        $archivos[] = $salida;

        try {
            errores::$error = false;
            $resultado = (new _expediente())->une_pdfs(
                documentos: array_column($ordenados, 'ruta'),
                ruta_salida: $salida
            );
            self::assertTrue($resultado);
            $lector = new Fpdi();
            self::assertSame(3, $lector->setSourceFile($salida));
            foreach (array(180, 140, 100) as $indice => $ancho) {
                $plantilla = $lector->importPage($indice + 1);
                $tamano = $lector->getTemplateSize($plantilla);
                self::assertEqualsWithDelta($ancho, $tamano['width'], 0.1);
            }
        } finally {
            foreach ($archivos as $archivo) {
                unlink($archivo);
            }
        }
    }

    public function testErrorDeQpdfNoSobrescribeSalida(): void
    {
        $entrada = tempnam(sys_get_temp_dir(), 'pdf_invalido_');
        $salida = tempnam(sys_get_temp_dir(), 'pdf_salida_');
        self::assertIsString($entrada);
        self::assertIsString($salida);
        file_put_contents($entrada, 'No es un PDF');
        file_put_contents($salida, 'contenido anterior');

        try {
            errores::$error = false;
            $resultado = (new _expediente())->une_pdfs(documentos: array($entrada), ruta_salida: $salida);
            self::assertIsArray($resultado);
            self::assertTrue(errores::$error);
            self::assertSame('contenido anterior', file_get_contents($salida));
        } finally {
            errores::$error = false;
            unlink($entrada);
            unlink($salida);
        }
    }

    public function testNoReemplazaUnArchivoDeEntrada(): void
    {
        $entrada = tempnam(sys_get_temp_dir(), 'pdf_entrada_');
        self::assertIsString($entrada);
        file_put_contents($entrada, 'original');

        try {
            errores::$error = false;
            $resultado = (new _expediente())->une_pdfs(documentos: array($entrada), ruta_salida: $entrada);
            self::assertIsArray($resultado);
            self::assertTrue(errores::$error);
            self::assertSame('original', file_get_contents($entrada));
        } finally {
            errores::$error = false;
            unlink($entrada);
        }
    }
}
