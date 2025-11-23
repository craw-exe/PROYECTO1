<?php
session_start();
include 'conexion.php';

// Determinar ID de usuario
if (isset($_SESSION['usuario'])) {
    $nombre_usuario = $_SESSION['usuario'];
    $stmt_usr = $conexion->prepare("SELECT id_usuario FROM Usuario WHERE nombre_usuario = ?");
    $stmt_usr->bind_param("s", $nombre_usuario);
    $stmt_usr->execute();
    $res = $stmt_usr->get_result();
    $usuario_data = $res->fetch_assoc();
    $id_usuario = $usuario_data['id_usuario'];
    $stmt_usr->close();
} elseif (isset($_SESSION['usuario_invitado'])) {
    $id_usuario = $_SESSION['usuario_invitado'];
} else {
    $id_usuario = null; // Ningún usuario aún
}

// Obtener los juegos de la biblioteca del usuario si existe
$juegos_biblioteca = [];
if ($id_usuario) {
    $query = $conexion->prepare("
        SELECT 
            b.id_biblioteca,
            b.fecha_adquirido,
            v.id_videojuego,
            v.titulo,
            v.descripcion,
            v.url_imagen,
            v.plataforma,
            v.fecha_lanzamiento,
            d.nombre_estudio AS desarrollador
        FROM Biblioteca b
        JOIN Videojuego v ON b.id_videojuego = v.id_videojuego
        JOIN Desarrollador d ON v.id_desarrollador = d.id_usuario
        WHERE b.id_usuario = ?
        ORDER BY b.fecha_adquirido DESC
    ");
    $query->bind_param("i", $id_usuario);
    $query->execute();
    $result = $query->get_result();
    $juegos_biblioteca = $result->fetch_all(MYSQLI_ASSOC);
    $query->close();
}

// Estadísticas
$total_juegos = count($juegos_biblioteca);

function limpiarRutaImagen($ruta) {
    $ruta = str_replace(['../', 'C:\\xampp\\htdocs\\PROYECTO1\\'], '', $ruta);
    $ruta = str_replace('\\', '/', $ruta);
    return $ruta;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Biblioteca - Xteam</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav class="navbar">
        <a href="index.php" class="logo">Xteam</a>
        <ul class="nav-links">
            <li><a href="index.php">Tienda</a></li>
            <li><a href="biblioteca.php">Biblioteca</a></li>
            <li><a href="carrito.php">Carrito</a></li>
            <li><a href="reviews.php">Reseñas</a></li>
            <li><a href="nosotros.php">Acerca de</a></li>
            <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'admin'): ?>
                    <li><a href="admin.php">Panel de Administrador</a></li>
                <?php endif; ?>
        </ul>
        <div class="nav-actions">
                <a href="login.html" class="login-btn">Iniciar Sesión</a>
            </div>
    </nav>
</header>

<main class="container">
    <div class="biblioteca-container">
        <h1>Mi Biblioteca</h1>
        <div class="biblioteca-stats">
            <div class="stat-card">
                <h3>Juegos Totales</h3>
                <div class="stat-number"><?php echo $total_juegos; ?></div>
            </div>
        </div>

        <div class="juegos-lista">
            <?php if ($total_juegos > 0): ?>
                <?php foreach ($juegos_biblioteca as $juego): ?>
                    <?php 
                    $imagen = limpiarRutaImagen($juego['url_imagen']);
                    $fecha_adquirido = date('d/m/Y', strtotime($juego['fecha_adquirido']));
                    ?>
                    <div class="juego-item">
                        <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($juego['titulo']); ?>" class="juego-imagen">
                        <div class="juego-info">
                            <h3 class="juego-titulo"><?php echo htmlspecialchars($juego['titulo']); ?></h3>
                            <div class="juego-meta">
                                <span class="juego-desarrollador"><?php echo htmlspecialchars($juego['desarrollador']); ?></span>
                                <span class="juego-plataforma"><?php echo htmlspecialchars($juego['plataforma']); ?></span>
                            </div>
                            <div class="juego-meta">
                                <span class="juego-fecha">Adquirido: <?php echo $fecha_adquirido; ?></span>
                                <span class="juego-lanzamiento">Lanzamiento: <?php echo date('d/m/Y', strtotime($juego['fecha_lanzamiento'])); ?></span>
                            </div>
                            <div class="juego-descripcion">
                                <p><?php echo htmlspecialchars(substr($juego['descripcion'], 0, 150) . '...'); ?></p>
                            </div>
                            <div class="juego-acciones">
                                <a href="jugar.php?id=<?php echo $juego['id_videojuego']; ?>" class="btn-jugar">🎮 Jugar</a>
                                <a href="detalleJuego.php?id=<?php echo $juego['id_videojuego']; ?>" class="btn-ver-detalles">📖 Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="biblioteca-vacia">
                    <div class="vacia-icon">🎮</div>
                    <h3>Tu biblioteca está vacía</h3>
                    <p>Compra algunos juegos para comenzar tu colección</p>
                    <a href="index.php" class="btn-primary">Explorar Tienda</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer>
    <a href="nosotros.php#contacto">Contáctanos</a><br><br>
    <p>&copy; 2025 Xteam. Todos los derechos reservados.</p>
</footer>
</body>
</html>


