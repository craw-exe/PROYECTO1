<?php
session_start();
include 'conexion.php';

// ======= CONSULTA PRINCIPAL: todos los juegos menos el de oferta =======
$juegos = $conexion->query("
    SELECT id_videojuego, titulo, precio, url_imagen, desarrollador, calificacion_promedio 
    FROM vw_catalogo_videojuegos 
    WHERE titulo != 'Left 4 Dead 2'
    ORDER BY id_videojuego ASC
");

// ======= CONSULTA OFERTA: solo Left 4 Dead 2 =======
$oferta = $conexion->query("
    SELECT id_videojuego, titulo, precio, url_imagen, desarrollador, calificacion_promedio 
    FROM vw_catalogo_videojuegos 
    WHERE titulo = 'Left 4 Dead 2' 
    LIMIT 1
");

// ======= FUNCIÓN para limpiar rutas =======
function limpiarRutaImagen($ruta) {
    $ruta = str_replace(['../', 'C:\\xampp\\htdocs\\PROYECTO1\\'], '', $ruta);
    $ruta = str_replace('\\', '/', $ruta); // estandariza separadores
    return $ruta;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xteam - Tu tienda de videojuegos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- ======= NAVBAR ======= -->
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">Xteam</a>
            <ul class="nav-links">
                <li><a href="index.php">Tienda</a></li>
                <li><a href="biblioteca.php">Biblioteca</a></li>
                <li><a href="carrito.php">Carrito</a></li>
                <li><a href="reviews.php">Reseñas</a></li>
                <li><a href="nosotros.php">Acerca de</a></li>
            </ul>

            <div class="nav-actions">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <p>Hola, <b><?php echo htmlspecialchars($_SESSION['usuario']); ?></b></p>
                    <a href="logout.php" class="login-btn">Cerrar sesión</a>
                <?php else: ?>
                    <a href="login.html" class="login-btn">Iniciar sesión</a>
                    <!-- <a href="registro.html" class="btn-register">Registrarse</a> -->
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- ======= CONTENIDO PRINCIPAL ======= -->
    <main class="container">
        <!-- JUEGOS DESTACADOS -->
        <section class="featured-games">
            <h2>Juegos Destacados</h2>
            <div class="game-grid">
                <?php if ($juegos && $juegos->num_rows > 0): ?>
                    <?php while($juego = $juegos->fetch_assoc()): 
                        $imagen = limpiarRutaImagen($juego['url_imagen']);
                    ?>
                        <div class="game-card">
                            <a href="detalleJuego.php?id=<?php echo $juego['id_videojuego']; ?>">
                                <img src="<?php echo htmlspecialchars($imagen); ?>" alt="Portada del juego">
                                <h3><?php echo htmlspecialchars($juego['titulo']); ?></h3>
                                <p class="price">$<?php echo number_format($juego['precio'], 2); ?></p>
                                <?php if (!empty($juego['calificacion_promedio'])): ?>
                                    <p class="rating">⭐ <?php echo number_format($juego['calificacion_promedio'], 1); ?>/5</p>
                                <?php endif; ?>
                            </a>

                            <form action="agregarCarrito.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $juego['id_videojuego']; ?>">
                                <input type="hidden" name="titulo" value="<?php echo htmlspecialchars($juego['titulo']); ?>">
                                <input type="hidden" name="precio" value="<?php echo $juego['precio']; ?>">
                                <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($imagen); ?>">
                                <button type="submit" class="btn-agregar-carrito">Agregar al Carrito</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay videojuegos disponibles en este momento.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- SECCIÓN DE OFERTAS -->
        <section class="special-offers">
            <h2>Ofertas Especiales</h2>
            <?php if ($oferta && $oferta->num_rows > 0): ?>
                <?php 
                    $of = $oferta->fetch_assoc();
                    $imagenOferta = limpiarRutaImagen($of['url_imagen']);
                ?>
                <div class="offer-card">
                    <a href="detalleJuego.php?id=<?php echo $of['id_videojuego']; ?>">
                        <img src="<?php echo htmlspecialchars($imagenOferta); ?>" alt="Oferta especial">
                    </a>
                    <div class="offer-details">
                        <h3><b><?php echo htmlspecialchars($of['titulo']); ?></b></h3>
                        <h3>¡Hasta 75% de descuento!</h3>
                        <p>Desarrollador: <?php echo htmlspecialchars($of['desarrollador']); ?></p>
                        <p>Aprovecha nuestras ofertas de temporada en títulos seleccionados.</p>

                        <form action="agregarCarrito.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $of['id_videojuego']; ?>">
                            <input type="hidden" name="titulo" value="<?php echo htmlspecialchars($of['titulo']); ?>">
                            <input type="hidden" name="precio" value="<?php echo $of['precio']; ?>">
                            <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($imagenOferta); ?>">
                            <button type="submit" class="btn-agregar-carrito">Agregar al Carrito</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <p>No hay ofertas disponibles actualmente.</p>
            <?php endif; ?>
        </section>
    </main>

    <!-- ======= FOOTER ======= -->
    <footer>
        <a href="nosotros.php#contacto">Contáctanos</a><br><br>
        <p>&copy; 2025 Xteam. Todos los derechos reservados.</p>
    </footer>
</body>
</html>

<?php
$conexion->close();
?>
