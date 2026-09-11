<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['id_usuario'])){

    header("Location: index.php?accion=login");

    exit();
}

?>

<?php include "views/partials/navbar.php"; ?>

<section class="ub-section">

    <div class="container">

        <div class="ub-hero" style="border-radius: var(--radius-lg); padding: 48px 44px; margin-bottom:36px;">

            <span class="ub-eyebrow on-dark">Tu espacio</span>

            <h2 class="mt-2 mb-2" style="color:var(--text-paper); font-size:30px;">
                Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?> 👋
            </h2>

            <p class="mb-0" style="color:var(--text-paper-muted);">
                Bienvenida nuevamente a UniBook.
            </p>

        </div>

        <div class="row g-4">

            <div class="col-md-4 reveal">
                <div class="ub-card ub-card-hover text-center p-4 h-100">
                    <div style="font-size:38px;">📚</div>
                    <h5 class="fw-bold mt-2 mb-2">Explorar libros</h5>
                    <p class="text-muted mb-4" style="font-size:14.5px;">
                        Descubre publicaciones de otros estudiantes.
                    </p>
                    <a href="index.php?accion=catalogo" class="btn-ub btn-ub-gold btn-ub-block">
                        Ver catálogo
                    </a>
                </div>
            </div>

            <div class="col-md-4 reveal">
                <div class="ub-card ub-card-hover text-center p-4 h-100">
                    <div style="font-size:38px;">➕</div>
                    <h5 class="fw-bold mt-2 mb-2">Publicar libro</h5>
                    <p class="text-muted mb-4" style="font-size:14.5px;">
                        Comparte material con la comunidad.
                    </p>
                    <a href="index.php?accion=publicar_libro" class="btn-ub btn-ub-gold btn-ub-block">
                        Publicar
                    </a>
                </div>
            </div>

            <div class="col-md-4 reveal">
                <div class="ub-card ub-card-hover text-center p-4 h-100">
                    <div style="font-size:38px;">👤</div>
                    <h5 class="fw-bold mt-2 mb-2">Mi perfil</h5>
                    <p class="text-muted mb-4" style="font-size:14.5px;">
                        Revisa y administra tus publicaciones.
                    </p>
                    <a href="index.php?accion=perfil" class="btn-ub btn-ub-gold btn-ub-block">
                        Ver perfil
                    </a>
                </div>
            </div>

        </div>

    </div>

</section>

<?php include "views/partials/footer.php"; ?>
