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

<div class="ub-section">
<div class="container">

    <?php if(!$libro){ ?>

        <!-- ================================================
             LIBRO NO ENCONTRADO
             (resguardo: antes esto generaba errores PHP al
             intentar leer $libro['...'] sobre un valor nulo)
             ================================================ -->
        <div class="catalog-empty reveal" style="max-width:520px; margin:0 auto;">
            <div class="catalog-empty__icon">📕</div>
            <h5 class="fw-bold mb-1">No encontramos ese libro</h5>
            <p class="text-muted mb-4">
                Puede que ya haya sido eliminado o el enlace sea incorrecto.
            </p>
            <a href="index.php?accion=catalogo" class="btn-ub btn-ub-dark">
                ← Volver al catálogo
            </a>
        </div>

    <?php }else{ ?>

        <?php
        $tipo = strtolower($libro['tipo_publicacion']);
        $stampClass = $tipo === 'venta' ? 'stamp-venta' : ($tipo === 'intercambio' ? 'stamp-intercambio' : 'stamp-prestamo');
        $tagClass = str_replace('stamp-', 'tag-', $stampClass);
        $esPropio = isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == $libro['usuario_id'];

        /*
         * FASE 3.1 — Disponibilidad de libros.
         * Igual que en catalogo.php: "?? 'disponible'" evita que la
         * página se rompa si el script SQL de esta fase todavía no
         * se ha ejecutado sobre la base de datos.
         */
        $disponibilidad = $libro['estado_disponibilidad'] ?? 'disponible';

        $dispoClases = [
            'disponible'    => 'badge-disp-disponible',
            'reservado'     => 'badge-disp-reservado',
            'vendido'       => 'badge-disp-vendido',
            'intercambiado' => 'badge-disp-intercambiado',
        ];
        $dispoClass = $dispoClases[$disponibilidad] ?? 'badge-disp-disponible';
        $dispoLabel = ucfirst($disponibilidad);
        ?>

        <a href="index.php?accion=catalogo" class="book-detail__back reveal">
            ← Volver al catálogo
        </a>

        <?php if(isset($_GET['compra'])){

            $mensajesCompra = [
                'propio'        => 'No puedes comprar tu propio libro.',
                'no_disponible' => 'Este libro ya no está disponible — alguien más lo reservó.',
                'tipo_no_venta' => 'Este libro no está publicado como Venta.',
                'invalido'      => 'Solicitud de compra inválida.',
                'error'         => 'No se pudo procesar tu compra. Intenta de nuevo.',
            ];

            $motivoCompra = $_GET['compra'];

            if(isset($mensajesCompra[$motivoCompra])){
        ?>
            <div class="ub-alert ub-alert-danger reveal" style="max-width:600px;">
                ⚠️ <?php echo $mensajesCompra[$motivoCompra]; ?>
            </div>
        <?php } } ?>

        <div class="book-detail">

            <!-- ============================================
                 PORTADA
                 ============================================ -->
            <div class="book-detail__cover reveal">

                <span class="tag <?php echo $tagClass; ?> book-detail__tag">
                    <?php echo $libro['tipo_publicacion']; ?>
                </span>

                <?php if(!empty($libro['imagen'])){ ?>
                    <img
                        src="<?php echo $libro['imagen']; ?>"
                        alt="<?php echo htmlspecialchars($libro['titulo']); ?>">
                <?php }else{ ?>
                    <div class="no-image">Sin portada</div>
                <?php } ?>

            </div>

            <!-- ============================================
                 INFORMACIÓN
                 ============================================ -->
            <div class="book-detail__info">

                <div class="book-detail__header reveal reveal-d1">

                    <?php if($esPropio){ ?>
                        <span class="stamp stamp-own mb-2 d-inline-block">
                            Tu publicación
                        </span>
                    <?php } ?>

                    <h1 class="book-detail__title">
                        <?php echo $libro['titulo']; ?>
                    </h1>

                    <p class="book-detail__author">
                        👨 <?php echo $libro['autor']; ?>
                        <?php if(!empty($libro['editorial'])){ ?>
                            &nbsp;·&nbsp; <?php echo $libro['editorial']; ?>
                        <?php } ?>
                    </p>

                    <div class="book-detail__price-row">

                        <span class="price book-detail__price">
                            $<?php echo number_format($libro['precio']); ?>
                        </span>

                        <span class="stamp stamp-neutral">
                            Estado: <?php echo $libro['estado']; ?>
                        </span>

                        <!-- FASE 3.1 — Badge de disponibilidad -->
                        <span class="stamp <?php echo $dispoClass; ?>">
                            <?php echo $dispoLabel; ?>
                        </span>

                    </div>

                    <!-- ====================================
                         FASE 3.2 — Acción de compra
                         Solo se muestra cuando: hay sesión,
                         no es el dueño, el tipo es "Venta" y
                         el libro está "disponible".
                         ==================================== -->
                    <div class="book-detail__actions">

                        <?php if(!isset($_SESSION['id_usuario'])){ ?>

                            <?php if($tipo === 'venta'){ ?>
                                <a href="index.php?accion=login" class="btn-ub btn-ub-dark btn-ub-block">
                                    Inicia sesión para comprar
                                </a>
                            <?php } ?>

                        <?php }elseif($esPropio){ ?>

                            <?php if($tipo === 'venta'){ ?>
                                <p class="text-muted mb-0" style="font-size:14px;">
                                    📌 Esta es tu publicación.
                                </p>
                            <?php } ?>

                        <?php }elseif($tipo === 'venta' && $disponibilidad === 'disponible'){ ?>

                            <a href="index.php?accion=checkout&id=<?php echo $libro['id']; ?>" class="btn-ub btn-ub-gold btn-ub-block">
                                🛒 Comprar libro
                            </a>

                        <?php }elseif($tipo === 'venta'){ ?>

                            <p class="text-muted mb-0" style="font-size:14px;">
                                Este libro ya no está disponible para compra.
                            </p>

                        <?php } ?>

                    </div>

                </div>

                <div class="book-detail__meta reveal reveal-d2">

                    <div class="meta-item">
                        <span class="ub-eyebrow">Universidad</span>
                        <p><?php echo $libro['universidad']; ?></p>
                    </div>

                    <div class="meta-item">
                        <span class="ub-eyebrow">Carrera</span>
                        <p><?php echo $libro['carrera']; ?></p>
                    </div>

                    <div class="meta-item">
                        <span class="ub-eyebrow">Semestre</span>
                        <p><?php echo $libro['semestre']; ?></p>
                    </div>

                    <div class="meta-item">
                        <span class="ub-eyebrow">Materia</span>
                        <p><?php echo $libro['materia']; ?></p>
                    </div>

                </div>

                <div class="book-detail__description reveal reveal-d2">
                    <span class="ub-eyebrow">Descripción</span>
                    <p><?php echo nl2br($libro['descripcion']); ?></p>
                </div>

                <?php if($vendedor){ ?>

                    <!-- ========================================
                         TARJETA DE VENDEDOR
                         Datos obtenidos vía UsuarioController::obtenerPorId()
                         a partir del usuario_id guardado en el libro.
                         ======================================== -->
                    <div class="seller-card reveal reveal-d3">

                        <div class="seller-card__avatar">👤</div>

                        <div class="seller-card__info">
                            <span class="ub-eyebrow">Publicado por</span>
                            <h5><?php echo htmlspecialchars($vendedor['nombre'] . ' ' . $vendedor['apellido']); ?></h5>
                            <p class="text-muted mb-0" style="font-size:13.5px;">
                                Estudiante verificado de UniBook
                            </p>
                        </div>

                        <?php if(!$esPropio && !empty($vendedor['correo'])){ ?>
                            <a
                                href="mailto:<?php echo htmlspecialchars($vendedor['correo']); ?>"
                                class="btn-ub btn-ub-outline btn-ub-sm seller-card__cta">
                                ✉️ Contactar
                            </a>
                        <?php } ?>

                    </div>

                <?php } ?>

            </div>

        </div>

    <?php } ?>

</div>
</div>

<?php include "views/partials/footer.php"; ?>
