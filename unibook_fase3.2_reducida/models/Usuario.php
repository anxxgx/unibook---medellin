<?php

class Usuario {

    private $conexion;

    public function __construct($conexion){
        $this->conexion = $conexion;
    }


    /*
     * FASE 3.0 — Estabilización técnica.
     * Antes esta consulta concatenaba las variables directamente
     * en el SQL (vulnerable a inyección). Ahora usa una consulta
     * preparada. La firma del método y lo que hace no cambian:
     * sigue insertando el mismo registro con las mismas columnas.
     */
    public function registrar($nombre, $apellido, $correo, $password){

        $sql = "INSERT INTO usuarios
        (nombre, apellido, correo, password)
        VALUES
        (?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return false;
        }

        $stmt->bind_param(
            "ssss",
            $nombre,
            $apellido,
            $correo,
            $password
        );

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }


    /*
     * FASE 3.0 — Estabilización técnica.
     * Mismo motivo que "registrar()": se migra a consulta
     * preparada. Sigue devolviendo exactamente lo mismo que
     * antes (un arreglo asociativo o null), así que login()
     * en UsuarioController no necesita ningún cambio.
     */
    public function buscarPorCorreo($correo){

        $sql = "SELECT * FROM usuarios
                WHERE correo = ?";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return null;
        }

        $stmt->bind_param("s", $correo);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        $stmt->close();

        return $usuario;
    }


    /*
     * Nuevo método (Fase 2 — Detalle del libro).
     * Se usa para mostrar la información del vendedor en el
     * detalle de un libro, a partir del "usuario_id" que ya
     * guarda la tabla "libros".
     *
     * Se implementa con una consulta preparada (más segura que
     * los métodos anteriores) porque es código nuevo; no modifica
     * el comportamiento de "registrar" ni "buscarPorCorreo".
     */
    public function buscarPorId($id){

        $sql = "SELECT id, nombre, apellido, correo
                FROM usuarios
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return null;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        $stmt->close();

        return $usuario;
    }

}