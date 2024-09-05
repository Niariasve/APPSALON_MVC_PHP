<?php

namespace Model;

class Usuario extends ActiveRecord {

    protected static $tabla = "Usuario";
    protected static $columnasDB = ["id", "nombre", "apellido", "email", "password", "telefono", "admin", "confirmado", "token"];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
    }

    public function validarNuevaCuenta() {
        if(!$this->nombre) $this->setAlerta('error', 'El nombre es obligatorio');
        if(!$this->apellido) $this->setAlerta('error', 'El apellido es obligatorio');
        if(!$this->email) $this->setAlerta('error', 'El email es obligatorio');      
        if(!$this->telefono) $this->setAlerta('error', 'El telefono es obligatorio');
        if(strlen($this->telefono) > 10) $this->setAlerta('error', 'Teléfono inválido');
        if(!$this->password) $this->setAlerta('error', 'El password es obligatorio');
        if(strlen($this->password) < 6) $this->setAlerta('error', 'El password debe tener al menos 6 caracteres');

        return self::$alertas;
    }

    public function validarLogin() {
        if(!$this->email) $this->setAlerta('error', 'El email es obligatorio');
        if(!$this->password) $this->setAlerta('error', 'El password es obligatorio');

        return self::$alertas;
    }

    public function validarEmail() {
        if(!$this->email) $this->setAlerta('error', 'El email es obligatorio');

        return self::$alertas;
    }

    public function validarPassword() {
        if(!$this->password) $this->setAlerta('error', 'El password es obligatorio');
        if(strlen($this->password) < 6) $this->setAlerta('error', 'El password debe tener al menos 6 caracteres');

        return self::$alertas;
    }

    //Revisa
    public function existeUsuario() {
        $query = "SELECT email FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1;";     
        $resultado = self::$db->query($query);
        
        if($resultado->num_rows) {
            $this->setAlerta('error', 'El usuario ya esta registrado');
        }

        return $resultado;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this->token = uniqid();
    }

    public function checkPassword($password) {
        return password_verify($password, $this->password);
    }

    public function checkConfirmado() {
        return $this->confirmado;
    }

    public function verificar($password) {
        return $this->checkPassword($password) && $this->checkConfirmado();
    }
}