<?php

/*
 * FASE 3.2 (alcance reducido) — Solo simulación de compra básica.
 *
 * A propósito este modelo tiene únicamente los 2 métodos que esta
 * fase necesita. Métodos como aceptar(), rechazar(),
 * obtenerPorComprador() u obtenerPorVendedor() se agregarán en la
 * siguiente fase (Mis compras / Mis ventas), sin tocar lo que ya
 * esté funcionando aquí.
 *
 * Mismo estilo del resto del proyecto (una clase por tabla, recibe
 * la conexión por constructor), con consultas 100% preparadas.
 */
class Compra {

    private $conexion;

    public function __construct($conexion){
        $this->conexion = $conexion;
    }


    /*
     * Crea el registro de compra. Nace siempre en 'pendiente'
     * (el DEFAULT de la columna). Devuelve el id de la compra
     * creada, o false si algo falló.
     */
    public function crear($libro_id, $comprador_id, $vendedor_id, $precio, $metodo_pago){

        $sql = "INSERT INTO compras
                (libro_id, comprador_id, vendedor_id, precio, metodo_pago)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return false;
        }

        $stmt->bind_param(
            "iiids",
            $libro_id,
            $comprador_id,
            $vendedor_id,
            $precio,
            $metodo_pago
        );

        $ok = $stmt->execute();

        $id = $this->conexion->insert_id;

        $stmt->close();

        return $ok ? $id : false;
    }


    /*
     * Detalle completo de una compra (libro + comprador + vendedor),
     * usado en la pantalla de éxito. Solo devuelve algo si quien
     * consulta es el comprador o el vendedor de esa compra —
     * autorización aplicada directamente en el WHERE, no solo
     * confiando en el id que llega por la URL.
     */
    public function obtenerDetalleParaUsuario($compra_id, $usuario_id){

        $sql = "SELECT
                    c.id, c.estado, c.precio, c.metodo_pago, c.fecha_compra,
                    l.id AS libro_id, l.titulo, l.autor, l.imagen,
                    comp.id AS comprador_id, comp.nombre AS comprador_nombre, comp.apellido AS comprador_apellido,
                    vend.id AS vendedor_id, vend.nombre AS vendedor_nombre, vend.apellido AS vendedor_apellido, vend.correo AS vendedor_correo
                FROM compras c
                INNER JOIN libros l ON l.id = c.libro_id
                INNER JOIN usuarios comp ON comp.id = c.comprador_id
                INNER JOIN usuarios vend ON vend.id = c.vendedor_id
                WHERE c.id = ?
                AND (c.comprador_id = ? OR c.vendedor_id = ?)";

        $stmt = $this->conexion->prepare($sql);

        if(!$stmt){
            return null;
        }

        $stmt->bind_param("iii", $compra_id, $usuario_id, $usuario_id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();

        $stmt->close();

        return $fila;
    }

}
