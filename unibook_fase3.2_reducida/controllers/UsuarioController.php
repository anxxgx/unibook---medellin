<?php

require_once "models/Usuario.php";

class UsuarioController {

    private $modelo;


    public function __construct($conexion){

        $this->modelo = new Usuario($conexion);

    }


    public function registrar(){

        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $correo = $_POST['correo'];

        $password = password_hash(
            $_POST['password'],
            PASSWORD_DEFAULT
        );


        $this->modelo->registrar(
            $nombre,
            $apellido,
            $correo,
            $password
        );

    }


    public function login(){

        $correo = $_POST['correo'];
        $password = $_POST['password'];


        $usuario = $this->modelo->buscarPorCorreo($correo);


        if($usuario){

            if(password_verify(
                $password,
                $usuario['password']
            )){


                if(session_status() === PHP_SESSION_NONE){
                    session_start();
                }


                $_SESSION['id_usuario'] = $usuario['id'];

                $_SESSION['nombre'] = $usuario['nombre'];


                header(
                    "Location: index.php?accion=dashboard"
                );

                exit();


            }else{

                echo "Contraseña incorrecta";

            }


        }else{

            echo "Usuario no encontrado";

        }

    }


    /*
     * Nuevo método (Fase 2 — Detalle del libro).
     * Devuelve los datos públicos del vendedor de un libro,
     * a partir de su usuario_id, para mostrarlos en el detalle.
     */
    public function obtenerPorId($id){

        return $this->modelo->buscarPorId($id);

    }

}