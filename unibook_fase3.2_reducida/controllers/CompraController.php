<?php

require_once "models/Compra.php";
require_once "models/Libro.php";
require_once "models/Usuario.php";

/*
 * FASE 3.2 (alcance reducido) — Solo simulación de compra básica.
 *
 * Sigue el mismo patrón que UsuarioController/LibroController:
 * recibe la conexión, instancia sus propios modelos, y expone
 * métodos que index.php invoca desde el router de acciones.
 *
 * A propósito solo existen 3 métodos en esta fase. aceptar(),
 * rechazar(), misCompras() y misVentas() se agregan en la
 * siguiente fase (Mis compras / Mis ventas), sin tocar nada de
 * lo que se construya aquí.
 *
 * Ninguno de estos métodos confía en validaciones hechas antes
 * (por ejemplo, en la vista que mostró el botón "Comprar"). Cada
 * método vuelve a validar todo desde cero cuando recibe el POST,
 * porque un formulario se puede manipular desde el navegador.
 */
class CompraController {

    private $modelo;
    private $libroModelo;
    private $usuarioModelo;

    public function __construct($conexion){
        $this->modelo = new Compra($conexion);
        $this->libroModelo = new Libro($conexion);
        $this->usuarioModelo = new Usuario($conexion);
    }


    /*
     * Pantalla de checkout (GET). Revalida todo lo necesario para
     * decidir si se puede mostrar el resumen de compra, sin
     * modificar nada todavía en la base de datos.
     */
    public function prepararCheckout($id){

        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        $resultado = [
            'valido'   => false,
            'motivo'   => null,
            'libro'    => null,
            'vendedor' => null,
        ];

        if(!isset($_SESSION['id_usuario'])){
            $resultado['motivo'] = 'sesion';
            return $resultado;
        }

        $id = (int)$id;

        if($id <= 0){
            $resultado['motivo'] = 'invalido';
            return $resultado;
        }

        $libro = $this->libroModelo->obtenerPorId($id);

        if(!$libro){
            $resultado['motivo'] = 'invalido';
            return $resultado;
        }

        $usuario_id = (int)$_SESSION['id_usuario'];

        if((int)$libro['usuario_id'] === $usuario_id){
            $resultado['motivo'] = 'propio';
            return $resultado;
        }

        if(strtolower($libro['tipo_publicacion']) !== 'venta'){
            $resultado['motivo'] = 'tipo_no_venta';
            return $resultado;
        }

        $disponibilidad = $libro['estado_disponibilidad'] ?? 'disponible';

        if($disponibilidad !== 'disponible'){
            $resultado['motivo'] = 'no_disponible';
            return $resultado;
        }

        $vendedor = $this->usuarioModelo->buscarPorId($libro['usuario_id']);

        $resultado['valido']   = true;
        $resultado['libro']    = $libro;
        $resultado['vendedor'] = $vendedor;

        return $resultado;
    }


    /*
     * Confirmar compra (POST). Vuelve a validar todo (nunca confía
     * en lo que ya se revisó al mostrar el checkout) y usa el
     * "candado atómico" de Libro::reservarSiDisponible() para que
     * dos compradores no puedan reservar el mismo libro dos veces.
     */
    public function confirmar(){

        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        if(!isset($_SESSION['id_usuario'])){
            return ['ok' => false, 'motivo' => 'sesion', 'libro_id' => 0];
        }

        $libro_id = (int)($_POST['libro_id'] ?? 0);
        $comprador_id = (int)$_SESSION['id_usuario'];

        $metodosValidos = ['pse', 'nequi', 'bancolombia', 'daviplata'];
        $metodo_pago = $_POST['metodo_pago'] ?? '';

        if(!in_array($metodo_pago, $metodosValidos, true)){
            $metodo_pago = 'pse';
        }

        if($libro_id <= 0){
            return ['ok' => false, 'motivo' => 'invalido', 'libro_id' => $libro_id];
        }

        // Verificamos que el libro exista de verdad en la base de datos.
        $libro = $this->libroModelo->obtenerPorId($libro_id);

        if(!$libro){
            return ['ok' => false, 'motivo' => 'invalido', 'libro_id' => $libro_id];
        }

        // Verificamos que el comprador exista de verdad (defensa extra,
        // por si la sesión quedara con un id_usuario obsoleto).
        $comprador = $this->usuarioModelo->buscarPorId($comprador_id);

        if(!$comprador){
            return ['ok' => false, 'motivo' => 'sesion', 'libro_id' => $libro_id];
        }

        $vendedor_id = (int)$libro['usuario_id'];

        // No permitir comprar el propio libro.
        if($vendedor_id === $comprador_id){
            return ['ok' => false, 'motivo' => 'propio', 'libro_id' => $libro_id];
        }

        // Verificamos que el vendedor exista de verdad.
        $vendedor = $this->usuarioModelo->buscarPorId($vendedor_id);

        if(!$vendedor){
            return ['ok' => false, 'motivo' => 'error', 'libro_id' => $libro_id];
        }

        // Solo se compra lo publicado como "Venta".
        if(strtolower($libro['tipo_publicacion']) !== 'venta'){
            return ['ok' => false, 'motivo' => 'tipo_no_venta', 'libro_id' => $libro_id];
        }

        /*
         * Reserva atómica: si esto devuelve false, es porque el
         * libro ya no estaba 'disponible' en este preciso instante
         * (ya fue comprado, reservado o alguien se adelantó).
         */
        $reservado = $this->libroModelo->reservarSiDisponible($libro_id);

        if(!$reservado){
            return ['ok' => false, 'motivo' => 'no_disponible', 'libro_id' => $libro_id];
        }

        // El precio SIEMPRE se toma del libro en base de datos,
        // nunca de lo que venga en el formulario.
        $precio = $libro['precio'];

        $compra_id = $this->modelo->crear(
            $libro_id,
            $comprador_id,
            $vendedor_id,
            $precio,
            $metodo_pago
        );

        if(!$compra_id){

            // Si el INSERT falla por algún motivo, revertimos la
            // reserva para no dejar un libro "atascado".
            $this->libroModelo->marcarDisponible($libro_id);

            return ['ok' => false, 'motivo' => 'error', 'libro_id' => $libro_id];
        }

        return [
            'ok'        => true,
            'motivo'    => 'reservada',
            'libro_id'  => $libro_id,
            'compra_id' => $compra_id,
        ];
    }


    /*
     * Detalle para la pantalla de éxito, verificando que quien
     * consulta sea el comprador o el vendedor de esa compra.
     */
    public function confirmacion($compra_id, $usuario_id){

        $compra_id = (int)$compra_id;
        $usuario_id = (int)$usuario_id;

        if($compra_id <= 0 || $usuario_id <= 0){
            return null;
        }

        return $this->modelo->obtenerDetalleParaUsuario($compra_id, $usuario_id);
    }

}
