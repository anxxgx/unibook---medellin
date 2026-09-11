<?php

class Libro {

    private $conexion;

    public function __construct($conexion){
        $this->conexion = $conexion;
    }


    /*
     * FASE 3.0 — Estabilización técnica.
     * Antes esta consulta concatenaba las 13 variables directamente
     * en el SQL. Ahora usa una consulta preparada. Firma, columnas
     * y comportamiento (devuelve el resultado de execute()) no cambian.
     */
    public function publicar(
        $titulo,
        $autor,
        $editorial,
        $universidad,
        $carrera,
        $semestre,
        $materia,
        $descripcion,
        $precio,
        $estado,
        $tipo_publicacion,
        $imagen,
        $usuario_id
    ){

        $sql = "INSERT INTO libros
        (
            titulo,
            autor,
            editorial,
            universidad,
            carrera,
            semestre,
            materia,
            descripcion,
            precio,
            estado,
            tipo_publicacion,
            imagen,
            usuario_id
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return false;
        }

        $stmt->bind_param(
            "sssssssdssssi",
            $titulo,
            $autor,
            $editorial,
            $universidad,
            $carrera,
            $semestre,
            $materia,
            $descripcion,
            $precio,
            $estado,
            $tipo_publicacion,
            $imagen,
            $usuario_id
        );

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }


    /*
     * Sin cambios: no recibe ningún parámetro externo, así que no
     * concatena nada y no tiene riesgo de inyección SQL. Convertirla
     * a consulta preparada no aportaría seguridad, solo ruido.
     */
    public function obtenerTodos(){

        $sql = "SELECT * FROM libros
                ORDER BY fecha_publicacion DESC";

        return $this->conexion->query($sql);
    }


    /*
     * FASE 3.0 — Estabilización técnica.
     * Migrada a consulta preparada. Sigue devolviendo un arreglo
     * asociativo (o null si no existe), igual que antes.
     */
    public function obtenerPorId($id){

        $sql = "SELECT * FROM libros
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return null;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $libro = $resultado->fetch_assoc();

        $stmt->close();

        return $libro;
    }


    /*
     * FASE 3.0 — Estabilización técnica.
     * Migrada a consulta preparada. Sigue devolviendo un
     * mysqli_result (con ->num_rows y ->fetch_assoc()), igual
     * que antes, para no afectar a perfil.php.
     */
    public function obtenerPorUsuario($usuario_id){

        $sql = "SELECT * FROM libros
                WHERE usuario_id = ?
                ORDER BY fecha_publicacion DESC";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return false;
        }

        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();

        $resultado = $stmt->get_result();

        $stmt->close();

        return $resultado;
    }


    /*
     * FASE 3.0 — Estabilización técnica.
     * Migrada a consulta preparada. Mantiene exactamente la misma
     * validación de seguridad (id + usuario_id en el WHERE) que ya
     * tenía, solo que ahora sin construir el SQL a mano.
     */
    public function eliminar($id, $usuario_id){

        $sql = "DELETE FROM libros
                WHERE id = ?
                AND usuario_id = ?";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return false;
        }

        $stmt->bind_param("ii", $id, $usuario_id);

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }


    /*
     * FASE 3.2 — Compra simulada de libros.
     *
     * "Candado" atómico contra condiciones de carrera: este UPDATE
     * solo afecta una fila si el libro sigue en 'disponible' en
     * este preciso instante. Si dos personas confirman casi al
     * mismo tiempo, solo la primera consulta que llegue a MySQL
     * tendrá affected_rows > 0; la segunda recibe false de forma
     * automática, sin necesitar transacciones explícitas.
     *
     * CompraController SOLO debe crear el registro en "compras"
     * si este método devuelve true.
     */
    public function reservarSiDisponible($id){

        $sql = "UPDATE libros
                SET estado_disponibilidad = 'reservado'
                WHERE id = ? AND estado_disponibilidad = 'disponible'";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return false;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $afectadas = $stmt->affected_rows;

        $stmt->close();

        return $afectadas > 0;
    }


    /*
     * FASE 3.2 (alcance reducido) — Se usará más adelante cuando
     * exista "rechazar venta" (fase Mis ventas). Se agrega ya
     * mismo porque CompraController::confirmar() la necesita como
     * reversión de seguridad si el INSERT de la compra fallara.
     */
    public function marcarDisponible($id){

        $sql = "UPDATE libros
                SET estado_disponibilidad = 'disponible'
                WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return false;
        }

        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }

}
