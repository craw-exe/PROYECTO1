<?php
session_start(); // 🔹 Importante: antes de cualquier HTML
include 'conexion.php';

// Obtener el ID del videojuego desde la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de videojuego no válido.");
}

$id_juego = intval($_GET['id']);

// Consulta a la vista del catálogo
$query = $conexion->prepare("
    SELECT 
        id_videojuego,
        titulo,
        descripcion,
        fecha_lanzamiento,
        precio,
        url_imagen,
        plataforma,
        idiomas,
        desarrollador,
        pais,
        calificacion_promedio,
        total_resenas
    FROM vw_catalogo_videojuegos
    WHERE id_videojuego = ?
");
$query->bind_param("i", $id_juego);
$query->execute();
$result = $query->get_result();
$juego = $result->fetch_assoc();

if (!$juego) {
    die("No se encontró el videojuego solicitado.");
}

// Limpieza de ruta de imagen
function limpiarRutaImagen($ruta) {
    $ruta = str_replace(['../', 'C:\\xampp\\htdocs\\PROYECTO1\\'], '', $ruta);
    $ruta = str_replace('\\', '/', $ruta);
    return $ruta;
}
$imagen = limpiarRutaImagen($juego['url_imagen']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($juego['titulo']); ?> - Xteam</title>
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
                <input type="text" placeholder="Buscar juegos..." class="search-bar">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <p>Hola, <b><?php echo htmlspecialchars($_SESSION['usuario']); ?></b></p>
                    <a href="logout.php" class="login-btn">Cerrar sesión</a>
                <?php else: ?>
                    <a href="login.html" class="login-btn">Iniciar sesión</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- ======= CONTENIDO PRINCIPAL ======= -->
    <main class="container">
        <div class="purchase-container">
            <!-- Información del juego -->
            <div class="game-details">
                <div class="game-header">
                    <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($juego['titulo']); ?>" class="game-cover">
                    <div class="game-info">
                        <h1><?php echo htmlspecialchars($juego['titulo']); ?></h1>
                        <div class="game-meta">
                            <span class="developer">Desarrollador: <?php echo htmlspecialchars($juego['desarrollador']); ?></span>
                            <span class="publisher">País: <?php echo htmlspecialchars($juego['pais']); ?></span>
                            <span class="release-date">Lanzamiento: <?php echo htmlspecialchars($juego['fecha_lanzamiento']); ?></span>
                            <span class="rating">⭐ Calificación: <?php echo number_format($juego['calificacion_promedio'], 1); ?>/5 (<?php echo $juego['total_resenas']; ?> reseñas)</span>
                        </div>
                        <div class="game-tags">
                            <span class="tag"><?php echo htmlspecialchars($juego['plataforma']); ?></span>
                            <span class="tag"><?php echo htmlspecialchars($juego['idiomas']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="game-description">
                    <h3>Descripción</h3>
                    <p><?php echo nl2br(htmlspecialchars($juego['descripcion'])); ?></p>
                </div>
            </div>

            <!-- Panel de compra -->
            <div class="purchase-panel">
                <div class="price-section">
                    <div class="current-price">$<?php echo number_format($juego['precio'], 2); ?></div>
                </div>

                <div class="purchase-options">
                    <!-- Botón Comprar Ahora - Formulario directo -->
                    <form action="metodoPago.php" class="enlaceA" method="POST">
                        <input type="hidden" name="id_videojuego" value="<?php echo $juego['id_videojuego']; ?>">
                        <input type="hidden" name="titulo" value="<?php echo htmlspecialchars($juego['titulo']); ?>">
                        <input type="hidden" name="precio" value="<?php echo $juego['precio']; ?>">
                        <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($imagen); ?>">
                        <input type="hidden" name="compra_directa" value="1"> <!-- Indicador para compra directa -->
                        <button type="submit" class="btn-buy-now">Comprar Ahora</button>
                    </form>

                    <form action="agregarCarrito.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $juego['id_videojuego']; ?>">
                        <input type="hidden" name="titulo" value="<?php echo htmlspecialchars($juego['titulo']); ?>">
                        <input type="hidden" name="precio" value="<?php echo $juego['precio']; ?>">
                        <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($imagen); ?>">
                        <button type="submit" class="btn-agregar-carrito">Agregar al Carrito</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- ======= FOOTER ======= -->
    <footer>
        <a href="nosotros.php#contacto">Contáctanos</a><br><br>
        <p>&copy; 2025 Xteam. Todos los derechos reservados.</p>
    </footer>
</body>
</html>

<?php
$query->close();
$conexion->close();
?>