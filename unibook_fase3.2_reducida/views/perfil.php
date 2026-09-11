<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['id_usuario'])){
    header("Location: index.php?accion=login");
    exit();
}

$misLibros = $libroController->misPublicaciones($_SESSION['id_usuario']);
$totalLibros = $misLibros->num_rows;

/*
 * FASE 3.1 — Disponibilidad de libros.
 * Mapa reutilizado igual que en catalogo.php y detalle_libro.php
 * para mostrar el mismo badge también aquí, en "Mis publicaciones".
 */
$dispoClasesPerfil = [
    'disponible'    => 'badge-disp-disponible',
    'reservado'     => 'badge-disp-reservado',
    'vendido'       => 'badge-disp-vendido',
    'intercambiado' => 'badge-disp-intercambiado',
];

?>

<?php include "views/partials/navbar.php"; ?>

<div class="ub-section">
<div class="container">

    <?php if(isset($_GET['eliminado'])){ ?>
        <div class="ub-alert ub-alert-success">
            ✅ La publicación fue eliminada correctamente.
        </div>
    <?php } ?>

    <?php if(isset($_GET['error'])){ ?>
        <div class="ub-alert ub-alert-danger">
            ❌ No se pudo eliminar la publicación.
        </div>
    <?php } ?>

    <!-- PERFIL -->
    <div class="ub-card mb-4 reveal">

        <div class="p-4 p-md-5">

            <div class="row align-items-center g-3">

                <div class="col-md-2 text-center">
                    <div class="avatar-ring mx-auto">👤</div>
                </div>

                <div class="col-md-10">
                    <h2 class="fw-bold mb-1"><?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
                    <p class="text-muted mb-0">Usuario activo de UniBook</p>
                </div>

            </div>

        </div>

    </div>

    <!-- ESTADÍSTICAS -->
    <div class="row g-3 mb-4">

        <div class="col-md-4 reveal">
            <div class="stat-card">
                <div class="num"><?php echo $totalLibros; ?></div>
                <div class="lbl">📚 Publicaciones</div>
            </div>
        </div>

        <div class="col-md-4 reveal">
            <div class="stat-card">
                <div class="num">⭐</div>
                <div class="lbl">Estado activo</div>
            </div>
        </div>

        <div class="col-md-4 reveal">
            <div class="stat-card">
                <div class="num">🎓</div>
                <div class="lbl">Comunidad UniBook</div>
            </div>
        </div>

    </div>

    <!-- PUBLICACIONES -->
    <div class="ub-card p-4 reveal">

        <h3 class="fw-bold mb-3">📚 Mis publicaciones</h3>
        <hr style="border-color: var(--paper-line);">

        <?php if($misLibros->num_rows > 0){ ?>

            <?php
            mysqli_data_seek($misLibros, 0);
            while($libro = $misLibros->fetch_assoc()){

                /*
                 * "?? 'disponible'" evita romper esta vista si el
                 * script SQL de la Fase 3.1 todavía no se ha
                 * ejecutado sobre la base de datos.
                 */
                $disponibilidadLibro = $libro['estado_disponibilidad'] ?? 'disponible';
                $dispoClaseLibro = $dispoClasesPerfil[$disponibilidadLibro] ?? 'badge-disp-disponible';
                $dispoLabelLibro = ucfirst($disponibilidadLibro);
            ?>

                <div class="ub-card ub-card-hover mb-3">

                    <div class="p-3 p-md-4">

                        <div class="row align-items-center g-3">

                            <div class="col-md-7">

                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <h5 class="fw-bold mb-0"><?php echo $libro['titulo']; ?></h5>
                                    <span class="stamp <?php echo $dispoClaseLibro; ?>">
                                        <?php echo $dispoLabelLibro; ?>
                                    </span>
                                </div>

                                <p class="mb-1 text-muted" style="font-size:14px;">👨 <?php echo $libro['autor']; ?></p>
                                <p class="mb-0 price" style="font-size:18px;">$<?php echo number_format($libro['precio']); ?></p>

                            </div>

                            <div class="col-md-5 text-md-end">

                                <a href="index.php?accion=detalle_libro&id=<?php echo $libro['id']; ?>" class="btn-ub btn-ub-dark btn-ub-sm">
                                    👁 Ver
                                </a>

                                <form method="POST" action="index.php" class="d-inline" onsubmit="return confirmarEliminacion();">
                                    <input type="hidden" name="id" value="<?php echo $libro['id']; ?>">
                                    <button type="submit" name="eliminar_libro" class="btn-ub btn-ub-danger btn-ub-sm">
                                        🗑️ Eliminar
                                    </button>
                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            <?php } ?>

        <?php }else{ ?>

            <div class="ub-alert" style="background: var(--paper-dim); color: var(--text-muted);">
                📚 Aún no has publicado libros.
            </div>

        <?php } ?>

    </div>

</div>
</div>

<?php include "views/partials/footer.php"; ?>
