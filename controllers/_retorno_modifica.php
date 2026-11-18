<?php

namespace gamboamartin\inmuebles\controllers;

use gamboamartin\system\actions;
use PDO;
use stdClass;

class _retorno_modifica
{
    public function captura(int $registro_id, string $tabla): stdClass
    {
        if (isset($_POST['apartado_retorno'])) {
            $_POST['ancla'] = $_POST['apartado_retorno'];
            unset($_POST['apartado_retorno']);
        }

        $retorno = new stdClass();
        $retorno->siguiente_view = $_POST['btn_action_next'] ?? 'modifica';
        $retorno->seccion = $_POST['seccion_retorno'] ?? $tabla;
        $retorno->registro_id = $_POST['id_retorno'] ?? $registro_id;
        $retorno->params = $_POST['params'] ?? array();
        $retorno->ancla = $_POST['ancla'] ?? '';
        return $retorno;
    }

    public function link(PDO $link, stdClass $retorno): array|string
    {
        return (new actions())->retorno_modifica_bd(
            link: $link, registro_id: $retorno->registro_id, seccion: $retorno->seccion,
            siguiente_view: $retorno->siguiente_view, params: $retorno->params,
            valida_permiso: true, ancla: $retorno->ancla
        );
    }
}
