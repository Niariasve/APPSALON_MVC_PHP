<?php 

namespace Controllers;

use Model\ActiveRecord;
use Model\AdminCita;
use MVC\Router;

class AdminController {
    public static function index(Router $router) {
        session_start();

        isAdmin();

        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $fechas = explode("-", $fecha);

        if (!checkdate($fechas[1], $fechas[2], $fechas[0])) {
            header('Location: /404');
        }

        //Consultar la base de datos
        $consulta = "SELECT cita.id, cita.hora, CONCAT( usuario.nombre, ' ', usuario.apellido) as cliente, ";
        $consulta .= " usuario.email, usuario.telefono, servicio.nombre as servicio, servicio.precio  ";
        $consulta .= " FROM cita  ";
        $consulta .= " LEFT OUTER JOIN usuario ";
        $consulta .= " ON cita.usuarioId=usuario.id  ";
        $consulta .= " LEFT OUTER JOIN citasservicios ";
        $consulta .= " ON citasservicios.citaId=cita.id ";
        $consulta .= " LEFT OUTER JOIN servicio ";
        $consulta .= " ON servicio.id=citasservicios.servicioId ";
        $consulta .= " WHERE fecha =  '$fecha' ";

        $citas = AdminCita::SQL($consulta);
        
        $router->render('/admin/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha
        ]);
    }
}