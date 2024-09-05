<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Classes\Mail;

class LoginController {

    public static function login(Router $router) {
        $alertas = [];

        if(isPostMethod()) {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario) {
                    if($usuario->verificar($auth->password)) {
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionamiento
                        if($usuario->admin) {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    } else {
                        Usuario::setAlerta('error', 'La contraseña es incorrecta o el usuario no esta confirmado');
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no econtrado');
                }
            }
        }
        
        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if(isPostMethod()) {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->checkConfirmado()) {
                    //Generar token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //enviar el email
                    $mail = new Mail($usuario->email, $usuario->nombre, $usuario->token);
                    $mail->enviarInstrucciones();

                    //Alerta de exito
                    $usuario->setAlerta('exito', 'Revisa tu email');

                } else {
                    Usuario::setAlerta('error', 'Usuario no existe o no esta confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start();

        $_SESSION = [];
        header('Location: /');
    }

    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if(!$usuario) {
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
        }

        if(isPostMethod()) {
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();

                if($resultado) {
                    header('location: /');
                }
            } 
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {

        $usuario = new Usuario();
        $alertas = [];

        if(isPostMethod()) {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)) {
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    $usuario->hashPassword();
                    $usuario->crearToken();
                    //Enviar el email
                    $mail = new Mail($usuario->email, $usuario->nombre, $usuario->token);
                    $mail->enviarConfirmacion();

                    //Crear el usuario
                    $resultado = $usuario->guardar();
                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }
        
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no es válido');
        } else {
            $usuario->confirmado = '1';
            $usuario->token = '';
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Token confirmado correctamente');
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}