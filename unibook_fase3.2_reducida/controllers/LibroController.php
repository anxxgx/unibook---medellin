<?php

require_once "models/Libro.php";

class LibroController {

    private $modelo;


    public function __construct($conexion){

        $this->modelo = new Libro($conexion);

    }


    public function publicar(){

        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        $rutaImagen = "";

        if(
            isset($_FILES['imagen']) &&
            $_FILES['imagen']['error'] == 0
        ){

            $nombreImagen =
                time() . "_" .
                basename($_FILES['imagen']['name']);

            $rutaImagen =
                "assets/uploads/" .
                $nombreImagen;

            move_uploaded_file(
                $_FILES['imagen']['tmp_name'],
                $rutaImagen
            );
        }


        $this->modelo->publicar(

            $_POST['titulo'],
            $_POST['autor'],
            $_POST['editorial'],
            $_POST['universidad'],
            $_POST['carrera'],
            $_POST['semestre'],
            $_POST['materia'],
            $_POST['descripcion'],
            $_POST['precio'],
            $_POST['estado'],
            $_POST['tipo_publicacion'],
            $rutaImagen,
            $_SESSION['id_usuario']

        );

    }


    public function listar(){

        return $this->modelo->obtenerTodos();

    }


    public function detalle($id){

        return $this->modelo->obtenerPorId($id);

    }


    public function misPublicaciones($usuario_id){

        return $this->modelo->obtenerPorUsuario(
            $usuario_id
        );

    }


    public function eliminar($id){

        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        $usuario_id = $_SESSION['id_usuario'];


        /*
         * Primero buscamos el libro para obtener
         * la imagen que tiene guardada.
         */
        $libro =
            $this->modelo->obtenerPorId($id);


        if(!$libro){

            return false;

        }


        /*
         * Seguridad:
         * comprobamos que el libro pertenece
         * al usuario que está conectado.
         */
        if(
            $libro['usuario_id'] != $usuario_id
        ){

            return false;

        }


        /*
         * Eliminamos el registro de la base de datos.
         */
        $resultado =
            $this->modelo->eliminar(
                $id,
                $usuario_id
            );


        /*
         * Si se eliminó correctamente,
         * eliminamos también la imagen.
         */
        if(
            $resultado &&
            !empty($libro['imagen'])
        ){

            $rutaCompleta =
                __DIR__ .
                "/../" .
                $libro['imagen'];


            if(file_exists($rutaCompleta)){

                unlink($rutaCompleta);

            }

        }


        return $resultado;

    }

}