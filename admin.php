<?php
session_start();
include("conexion.php");

// Verificar que sea administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Obtener estadísticas desde la vista vw_estadisticas_admin
$estadisticas = $conexion->query("SELECT * FROM vw_estadisticas_admin")->fetch_assoc();

// Consultas de datos
$usuarios = $conexion->query("
    SELECT id_usuario, nombre_usuario, email, tipo_usuario, fecha_registro 
    FROM Usuario ORDER BY id_usuario 
");

$videojuegos = $conexion->query("
    SELECT v.id_videojuego, v.titulo, d.nombre_estudio AS desarrollador, 
           v.precio, v.fecha_lanzamiento 
    FROM Videojuego v
    JOIN Desarrollador d ON v.id_desarrollador = d.id_usuario
    ORDER BY v.fecha_lanzamiento DESC
");

$compras = $conexion->query("
    SELECT c.id_compra, u.nombre_usuario AS usuario, c.total, c.metodo_pago, 
           c.fecha_compra, c.estado
    FROM Compra c
    LEFT JOIN Usuario u ON c.id_usuario = u.id_usuario
    ORDER BY c.fecha_compra DESC
");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Xteam</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Panel de Administración - Xteam</h1>
            <div class="admin-nav">
                <a href="#estadisticas">Estadísticas</a>
                <a href="#usuarios">Usuarios</a>
                <a href="#videojuegos">Videojuegos</a>
                <a href="#compras">Compras</a>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </header>

        <main class="admin-main">
            <!-- Sección de estadísticas -->
            <section id="estadisticas" class="admin-section">
                <h2>Estadísticas Generales</h2>
                <div class="stats-grid">
                    <div class="stat-card"><h3>Total Clientes</h3><div class="stat-number"><?= $estadisticas['total_clientes'] ?></div></div>
                    <div class="stat-card"><h3>Total Desarrolladores</h3><div class="stat-number"><?= $estadisticas['total_desarrolladores'] ?></div></div>
                    <div class="stat-card"><h3>Total Videojuegos</h3><div class="stat-number"><?= $estadisticas['total_videojuegos'] ?></div></div>
                    <div class="stat-card"><h3>Total Ventas</h3><div class="stat-number"><?= $estadisticas['total_ventas'] ?></div></div>
                    <div class="stat-card"><h3>Ingresos Totales</h3><div class="stat-number">$<?= number_format($estadisticas['ingresos_totales'], 2) ?></div></div>
                    <div class="stat-card"><h3>Total Reseñas</h3><div class="stat-number"><?= $estadisticas['total_resenas'] ?></div></div>
                </div>
            </section>

            <!-- Sección de usuarios -->
            <section id="usuarios" class="admin-section">
                <h2>Usuarios Registrados</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($u = $usuarios->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $u['id_usuario'] ?></td>
                                    <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= ucfirst($u['tipo_usuario']) ?></td>
                                    <td><?= $u['fecha_registro'] ?></td>
                                    <td>
                                        <a href="editar_usuario.php?id=<?= $u['id_usuario'] ?>">Editar</a>
                                        <a href="eliminar_usuario.php?id=<?= $u['id_usuario'] ?>" onclick="return confirm('¿Eliminar este usuario?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Sección de videojuegos -->
            <section id="videojuegos" class="admin-section">
                <h2>Videojuegos Registrados</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Desarrollador</th>
                                <th>Precio</th>
                                <th>Fecha Lanzamiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($v = $videojuegos->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $v['id_videojuego'] ?></td>
                                    <td><?= htmlspecialchars($v['titulo']) ?></td>
                                    <td><?= htmlspecialchars($v['desarrollador']) ?></td>
                                    <td>$<?= number_format($v['precio'], 2) ?></td>
                                    <td><?= $v['fecha_lanzamiento'] ?></td>
                                    <td>
                                        <a href="editar_juego.php?id=<?= $v['id_videojuego'] ?>">Editar</a>
                                        <a href="eliminar_juego.php?id=<?= $v['id_videojuego'] ?>" onclick="return confirm('¿Eliminar este videojuego?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Sección de compras -->
            <section id="compras" class="admin-section">
                <h2>Historial de Compras</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Total</th>
                                <th>Método</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($c = $compras->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $c['id_compra'] ?></td>
                                    <td><?= htmlspecialchars($c['usuario']) ?: 'Invitado' ?></td>
                                    <td>$<?= number_format($c['total'], 2) ?></td>
                                    <td><?= ucfirst($c['metodo_pago']) ?></td>
                                    <td><?= $c['fecha_compra'] ?></td>
                                    <td><?= ucfirst($c['estado']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>