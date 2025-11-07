<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];
$mensaje = "";
$compra = null;
$detalles = [];

// Verificar si viene desde el receptor
if (isset($_GET['compra']) && $_GET['compra'] === "ok") {
    $mensaje = "✅ ¡Gracias por tu compra! Tu transacción fue completada correctamente.";

    // Obtener la última compra del usuario
    $sql = "SELECT * FROM Compra WHERE id_usuario = ? ORDER BY fecha_compra DESC LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $compra = $resultado->fetch_assoc();
    $stmt->close();

    if ($compra) {
        // Obtener los detalles de esa compra
        $sql_detalle = "SELECT d.*, v.titulo, v.imagen, v.precio
                        FROM DetalleCompra d
                        INNER JOIN Videojuego v ON v.id_videojuego = d.id_videojuego
                        WHERE d.id_compra = ?";
        $stmt_detalle = $conexion->prepare($sql_detalle);
        $stmt_detalle->bind_param("i", $compra['id_compra']);
        $stmt_detalle->execute();
        $resultado_detalle = $stmt_detalle->get_result();
        $detalles = $resultado_detalle->fetch_all(MYSQLI_ASSOC);
        $stmt_detalle->close();
    }
} else {
    $mensaje = "⚠️ No se detectó ninguna compra reciente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Compra - Xteam</title>
    <link rel="stylesheet" href="css/estilos.css"> <!-- tu hoja de estilos -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0f0f0f;
            color: #fff;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 60px auto;
            background-color: #1a1a1a;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(255,255,255,0.1);
        }
        h1 {
            text-align: center;
            color: #00ff88;
        }
        .mensaje {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.2em;
        }
        .juegos {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .juego {
            background-color: #222;
            border-radius: 10px;
            padding: 15px;
            width: 200px;
            text-align: center;
        }
        .juego img {
            width: 100%;
            border-radius: 8px;
        }
        .btn-volver {
            display: block;
            width: fit-content;
            margin: 40px auto 0;
            padding: 10px 20px;
            background-color: #00ff88;
            color: #000;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn-volver:hover {
            background-color: #00cc6a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Confirmación de Compra</h1>
        <div class="mensaje"><?= $mensaje ?></div>

        <?php if ($compra && !empty($detalles)): ?>
            <h2>Total pagado: $<?= number_format($compra['total'], 2) ?> MXN</h2>
            <h3>Método de pago: <?= ucfirst($compra['metodo_pago']) ?></h3>
            <div class="juegos">
                <?php foreach ($detalles as $item): ?>
                    <div class="juego">
                        <img src="data:image/jpeg;base64,<?= base64_encode($item['imagen']) ?>" alt="<?= htmlspecialchars($item['titulo']) ?>">
                        <h4><?= htmlspecialchars($item['titulo']) ?></h4>
                        <p>$<?= number_format($item['precio_unitario'], 2) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn-volver">Volver a la Tienda</a>
    </div>
</body>
</html>
