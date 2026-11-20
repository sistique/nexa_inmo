<?php

namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;

class _expediente
{
    public function une_pdfs(array $documentos, string $ruta_salida): array|bool
    {
        if (count($documentos) === 0 || trim($ruta_salida) === '') {
            return (new errores())->error(mensaje: 'Se requieren documentos y una ruta de salida', data: $ruta_salida);
        }

        $archivos = array();
        $salida_existente = realpath($ruta_salida);
        foreach ($documentos as $documento) {
            $ruta = is_string($documento) ? realpath($documento) : false;
            if ($ruta === false || !is_file($ruta) || !is_readable($ruta)) {
                return (new errores())->error(mensaje: 'El PDF no existe o no puede leerse', data: $documento);
            }
            if ($salida_existente !== false && $ruta === $salida_existente) {
                return (new errores())->error(mensaje: 'La salida no puede reemplazar un PDF de entrada', data: $ruta);
            }
            $archivos[] = $ruta;
        }

        $directorio = realpath(dirname($ruta_salida));
        if ($directorio === false || !is_dir($directorio) || !is_writable($directorio)) {
            return (new errores())->error(mensaje: 'El directorio de salida no está disponible', data: $ruta_salida);
        }
        $temporal = tempnam($directorio, 'qpdf_');
        if ($temporal === false || dirname($temporal) !== $directorio) {
            if ($temporal !== false) {
                unlink($temporal);
            }
            return (new errores())->error(mensaje: 'No se pudo crear el archivo temporal de salida', data: $directorio);
        }

        $errores_qpdf = tmpfile();
        if ($errores_qpdf === false) {
            unlink($temporal);
            return (new errores())->error(mensaje: 'No se pudo capturar la salida de qpdf', data: $ruta_salida);
        }

        $comando = array_merge(array('qpdf', '--empty', '--object-streams=disable', '--pages'), $archivos,
            array('--', $temporal));
        $proceso = proc_open($comando, array(
            0 => array('file', '/dev/null', 'r'),
            1 => array('file', '/dev/null', 'w'),
            2 => $errores_qpdf
        ), $pipes);
        if (!is_resource($proceso)) {
            fclose($errores_qpdf);
            unlink($temporal);
            return (new errores())->error(mensaje: 'No se pudo iniciar qpdf', data: $comando[0]);
        }

        $codigo = proc_close($proceso);
        rewind($errores_qpdf);
        $detalle = stream_get_contents($errores_qpdf);
        fclose($errores_qpdf);
        clearstatcache(true, $temporal);
        if ($codigo !== 0 || !is_file($temporal) || filesize($temporal) === 0) {
            unlink($temporal);
            return (new errores())->error(
                mensaje: 'qpdf no pudo unir los documentos',
                data: array('codigo' => $codigo, 'detalle' => $detalle, 'documentos' => $archivos)
            );
        }

        if (!rename($temporal, $ruta_salida)) {
            unlink($temporal);
            return (new errores())->error(mensaje: 'No se pudo guardar el expediente', data: $ruta_salida);
        }

        return true;
    }

    public function descarga_pdfs(array $documentos, string $nombre): array|bool
    {
        if ($nombre === '' || !preg_match('/\A[A-Za-z0-9 ._-]+\.pdf\z/i', $nombre)) {
            return (new errores())->error(mensaje: 'Nombre de descarga del expediente no válido', data: $nombre);
        }
        if (headers_sent($archivo, $linea)) {
            return (new errores())->error(
                mensaje: 'No se puede descargar el expediente porque ya se envió contenido',
                data: array('archivo' => $archivo, 'linea' => $linea)
            );
        }
        $temporal = tempnam(sys_get_temp_dir(), 'expediente_');
        if ($temporal === false) {
            return (new errores())->error(mensaje: 'No se pudo crear el expediente temporal', data: sys_get_temp_dir());
        }

        $resultado = $this->une_pdfs(documentos: $documentos, ruta_salida: $temporal);
        if (errores::$error) {
            unlink($temporal);
            return (new errores())->error(mensaje: 'Error al generar expediente para descarga', data: $resultado);
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Content-Length: ' . filesize($temporal));
        header('Cache-Control: private, max-age=0, must-revalidate');
        $leidos = readfile($temporal);
        unlink($temporal);
        if ($leidos === false) {
            return (new errores())->error(mensaje: 'No se pudo enviar el PDF del expediente', data: $nombre);
        }

        return true;
    }

    /**
     * Ordena los documentos por descripción de tipo, conservando el orden de selección dentro de cada grupo.
     *
     * @param array $documentos Filas con la clave doc_tipo_documento_descripcion.
     * @param string[] $orden_tipos Descripciones de tipos en orden de prioridad.
     * @return array Filas ordenadas; los tipos no listados quedan al final.
     */
    public function ordena_documentos(array $documentos, array $orden_tipos): array
    {
        $prioridades = array();
        foreach ($orden_tipos as $tipo) {
            $clave = mb_strtoupper(trim($tipo), 'UTF-8');
            if ($clave !== '' && !array_key_exists($clave, $prioridades)) {
                $prioridades[$clave] = count($prioridades);
            }
        }

        $grupos = array_fill(0, count($prioridades) + 1, array());
        foreach ($documentos as $documento) {
            $tipo = mb_strtoupper(trim($documento['doc_tipo_documento_descripcion']), 'UTF-8');
            $prioridad = $prioridades[$tipo] ?? count($prioridades);
            $grupos[$prioridad][] = $documento;
        }

        return array_merge(...$grupos);
    }
}
