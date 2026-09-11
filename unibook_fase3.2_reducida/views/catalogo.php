<?php if(session_status() === PHP_SESSION_NONE){ session_start(); } ?>

<?php if(isset($_SESSION['id_usuario'])){ ?>
    <?php include "views/partials/navbar.php"; ?>
<?php }else{ ?>
    <nav class="navbar ub-nav">
        <div class="container justify-content-between">
            <a class="navbar-brand" href="index.php"><span class="mark">📚</span> UniBook</a>
            <a href="index.php?accion=login" class="btn-ub btn-ub-outline on-dark btn-ub-sm">Iniciar sesión</a>
        </div>
    </nav>
<?php } ?>

<?php $totalLibrosCatalogo = $datos->num_rows; ?>

<!-- =========================================================
     HERO DEL CATÁLOGO
     Continuación visual del hero de la landing (mismo sistema
     ub-hero / ub-eyebrow / gold accents sobre fondo "ink").
     ========================================================= -->
<section class="catalog-hero">

    <div class="container">

        <div class="catalog-hero__inner reveal">

            <span class="ub-eyebrow on-dark">381.46 — Catálogo general</span>

            <h1 class="catalog-hero__title">
                Encuentra tu próximo <em>libro de texto.</em>
            </h1>

            <p class="catalog-hero__lead">
                <?php echo $totalLibrosCatalogo; ?> publicaciones activas de estudiantes
                de distintas universidades y carreras de Medellín.
            </p>

            <div class="catalog-search">
                <span class="catalog-search__icon" aria-hidden="true">🔍</span>
                <input
                    type="text"
                    id="buscador"
                    class="catalog-search__input"
                    placeholder="Buscar por título, autor o materia...">
            </div>

        </div>

    </div>

</section>

<div class="ub-section">
<div class="container">

    <div class="catalog-toolbar reveal">

        <p class="catalog-count" id="catalogoContador">
            Mostrando <b><?php echo $totalLibrosCatalogo; ?></b>
            <?php echo $totalLibrosCatalogo == 1 ? 'libro' : 'libros'; ?>
        </p>

    </div>

    <div class="row" id="contenedorLibros">

        <?php while($libro = $datos->fetch_assoc()){ ?>

            <div
                class="col-md-4 mb-4 libro-card reveal"

                data-titulo="<?php echo strtolower($libro['titulo']); ?>"
                data-autor="<?php echo strtolower($libro['autor']); ?>"
                data-materia="<?php echo strtolower($libro['materia']); ?>">

                <div class="book-card">

                    <?php
                    $tipo = strtolower($libro['tipo_publicacion']);
                    $stampClass = $tipo === 'venta' ? 'stamp-venta' : ($tipo === 'intercambio' ? 'stamp-intercambio' : 'stamp-prestamo');
                    $tagClass = str_replace('stamp-', 'tag-', $stampClass);

                    /*
                     * FASE 3.1 — Disponibilidad de libros.
                     * "estado_disponibilidad" es una columna nueva.
                     * Se usa "?? 'disponible'" como resguardo: si por
                     * alguna razón el script SQL de la Fase 3.1 aún
                     * no se ha ejecutado, el catálogo sigue funcionando
                     * exactamente igual que antes (todo se ve "disponible").
                     */
                    $disponibilidad = $libro['estado_disponibilidad'] ?? 'disponible';
                    $noDisponible = $disponibilidad !== 'disponible';

                    $dispoClases = [
                        'reservado'     => 'badge-disp-reservado',
                        'vendido'       => 'badge-disp-vendido',
                        'intercambiado' => 'badge-disp-intercambiado',
                    ];
                    $dispoClass = $dispoClases[$disponibilidad] ?? 'badge-disp-reservado';
                    $dispoLabel = ucfirst($disponibilidad);
                    ?>

                    <div class="cover<?php echo $noDisponible ? ' cover--unavailable' : ''; ?>">

                        <span class="tag <?php echo $tagClass; ?>"><?php echo $libro['tipo_publicacion']; ?></span>

                        <?php if(!empty($libro['imagen'])){ ?>
                            <img src="<?php echo $libro['imagen']; ?>" alt="<?php echo htmlspecialchars($libro['titulo']); ?>" loading="lazy">
                        <?php }else{ ?>
                            <div class="no-image">Sin portada</div>
                        <?php } ?>

                        <?php if($noDisponible){ ?>
                            <div class="cover__overlay">
                                <span class="stamp <?php echo $dispoClass; ?>">
                                    <?php echo $dispoLabel; ?>
                                </span>
                            </div>
                        <?php } ?>

                    </div>

                    <div class="body">

                        <h5><?php echo $libro['titulo']; ?></h5>

                        <div class="meta-row">
                            <span>👨 <b><?php echo $libro['autor']; ?></b></span>
                            <span>🏫 <?php echo $libro['universidad']; ?></span>
                            <span>🎓 <?php echo $libro['carrera']; ?> · Sem. <?php echo $libro['semestre']; ?></span>
                            <span>📖 <?php echo $libro['materia']; ?></span>
                        </div>

                        <div class="price">$<?php echo number_format($libro['precio']); ?></div>

                    </div>

                    <div class="footer">
                        <a href="index.php?accion=detalle_libro&id=<?php echo $libro['id']; ?>" class="btn-ub btn-ub-dark btn-ub-block">
                            Ver detalles
                        </a>
                    </div>

                </div>

            </div>

        <?php } ?>

    </div>

    <div class="catalog-empty" id="catalogoVacio" style="display:none;">
        <div class="catalog-empty__icon">📭</div>
        <h5 class="fw-bold mb-1">No encontramos libros con esa búsqueda</h5>
        <p class="text-muted mb-0">Intenta con otro título, autor o materia.</p>
    </div>

</div>
</div>

<?php include "views/partials/footer.php"; ?>
