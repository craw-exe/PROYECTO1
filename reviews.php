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
    <title>Reseñas - Xteam</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="image/png" href="imgs/logo.png?v=2">
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
        </nav>
    </header>

    <main class="container">
        <h1>Reseñas de la Comunidad</h1>
        <div class="review-list">
            <div class="review-card">
                <div class="review-header">
                    <h4>Nombre del Juego: Call of Duty Black Ops 3</h4>
                </div>
                <p class="review-author">Por: Tyreans</p>
                <p class="review-text">"Un juego increíble con una historia que te atrapa desde el primer minuto. Los gráficos son impresionantes y la jugabilidad es muy fluida. ¡Totalmente recomendado!"</p>
            </div>

            <div class="review-card">
                <div class="review-header">
                    <h4>Nombre del Juego: Dragon Ball FighterZ</h4>
                </div>
                <p class="review-author">Por: Pipoko</p>
                <p class="review-text">"Tiene un gran potencial, pero todavía le faltan algunas actualizaciones. El concepto es bueno, pero la ejecución podría ser mejor. Aún así, es entretenido."</p>
            </div>
             <div class="review-card">
                <div class="review-header">
                    <h4>Nombre del Juego: R.E.P.O.</h4>
                </div>
                <p class="review-author">Por: Patatapou</p>
                <p class="review-text">"¡Una obra maestra del género de misterio! La trama es compleja y te mantiene en vilo hasta el final."</p>
            </div>
        </div>
    </main>

    <footer>
        <a href="nosotros.html#contacto">Contactanos</a><br><br>
        <p>&copy; 2025 Xteam. Todos los derechos reservados.</p>
    </footer>
</body>
</html>