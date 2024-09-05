<?php

namespace Controllers;

use Model\Servicio;
use Model\Cita;
use Model\CitaServicio;
use MVC\Router;

class APIController {

    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar() {

        //Almacena la cita y devuelve el id
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado['id'];

        //Almacena la cita y el/los Servicios

        $idServicios = explode( ",", $_POST['servicios'] );

        foreach ($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        //Retornamos una respuesta
        $respuesta = [
            'resultado' => $resultado
        ];

        echo json_encode($respuesta);
    }   

    public static function eliminar() {
        
        if (isPostMethod()) {
            $id = $_POST['id'];
            $cita = Cita::find($id);
            $cita->eliminar();
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }
}