<?php

require_once "config/conexion.php";

require_once "controllers/UsuarioController.php";
require_once "controllers/LibroController.php";
require_once "controllers/CompraController.php";


$usuarioController =
    new UsuarioController($conexion);

$libroController =
    new LibroController($conexion);

$compraController =
    new CompraController($conexion);


$accion =
    $_GET['accion'] ?? 'registro';


// ==============================
// REGISTRO
// ==============================

if(isset($_POST['registrar'])){

    $usuarioController->registrar();

    $mensajeRegistro = "
        <div class='container mt-4'>
            <div class='ub-alert ub-alert-success'>
                ✅ Cuenta creada correctamente. Ya puedes iniciar sesión.
            </div>
        </div>
    ";

}


// ==============================
// LOGIN
// ==============================

if(isset($_POST['login'])){

    $usuarioController->login();

}


// ==============================
// PUBLICAR LIBRO
// ==============================

if(isset($_POST['publicar_libro'])){

    $libroController->publicar();

    $mensajePublicar = "
        <div class='ub-alert ub-alert-success'>
            ✅ Libro publicado correctamente.
        </div>
    ";

}


// ==============================
// ELIMINAR LIBRO
// ==============================

if(isset($_POST['eliminar_libro'])){

    $id =
        $_POST['id'] ?? 0;


    $eliminado =
        $libroController->eliminar($id);


    if($eliminado){

        header(
            "Location: index.php?accion=perfil&eliminado=1"
        );

        exit();

    }else{

        header(
            "Location: index.php?accion=perfil&error=1"
        );

        exit();

    }

}


// ==============================
// CONFIRMAR COMPRA (Fase 3.2)
// ==============================

if(isset($_POST['confirmar_compra'])){

    $resultado =
        $compraController->confirmar();

    if($resultado['motivo'] === 'sesion'){

        header("Location: index.php?accion=login");
        exit();

    }

    if($resultado['ok']){

        header(
            "Location: index.php?accion=compra_confirmada&id=" . $resultado['compra_id']
        );

        exit();

    }else{

        header(
            "Location: index.php?accion=detalle_libro&id=" . $resultado['libro_id'] . "&compra=" . $resultado['motivo']
        );

        exit();

    }

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>UniBook Medellín — Estudia más, paga menos.</title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📚</text></svg>">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="assets/css/style.css"
        rel="stylesheet">

</head>


<body>


<?php

switch($accion){


    // ==========================
    // LOGIN
    // ==========================

    case 'login':

        include "views/login.php";

    break;


    // ==========================
    // DASHBOARD
    // ==========================

    case 'dashboard':

        include "views/dashboard.php";

    break;


    // ==========================
    // PUBLICAR LIBRO
    // ==========================

    case 'publicar_libro':

        include "views/publicar_libro.php";

    break;


    // ==========================
    // CATÁLOGO
    // ==========================

    case 'catalogo':

        $datos =
            $libroController->listar();

        include "views/catalogo.php";

    break;


    // ==========================
    // DETALLE DEL LIBRO
    // ==========================

    case 'detalle_libro':

        $id =
            $_GET['id'] ?? 0;

        $libro =
            $libroController->detalle($id);

        $vendedor = null;

        if($libro){

            $vendedor =
                $usuarioController->obtenerPorId($libro['usuario_id']);

        }

        include "views/detalle_libro.php";

    break;


    // ==========================
    // PERFIL
    // ==========================

    case 'perfil':

        include "views/perfil.php";

    break;


    // ==========================
    // CHECKOUT (Fase 3.2)
    // ==========================

    case 'checkout':

        $idCheckout =
            $_GET['id'] ?? 0;

        $checkoutInfo =
            $compraController->prepararCheckout($idCheckout);

        include "views/checkout.php";

    break;


    // ==========================
    // COMPRA CONFIRMADA (Fase 3.2)
    // ==========================

    case 'compra_confirmada':

        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        if(!isset($_SESSION['id_usuario'])){
            header("Location: index.php?accion=login");
            exit();
        }

        $idCompraConfirmada =
            $_GET['id'] ?? 0;

        $compraConfirmada =
            $compraController->confirmacion($idCompraConfirmada, $_SESSION['id_usuario']);

        include "views/compra_confirmada.php";

    break;


    // ==========================
    // REGISTRO
    // ==========================

    default:

        include "views/registro.php";

    break;

}

?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>
