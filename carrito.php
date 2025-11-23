<?php
session_start();
// Aseguramos que carrito sea un array para evitar errores si es null
$carrito = $_SESSION['carrito'] ?? [];
$subtotal = 0;
$descuentos = 0;

foreach ($carrito as $producto) {
    $subtotal += $producto['precio'];
    $descuentos += $producto['descuento'];
}
$total = $subtotal - $descuentos;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - Xteam</title>
    <link rel="stylesheet" href="styleCarrito.css">
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
        <div class="carrito-container">
            <h1>Tu Carrito de Compras</h1>
            <div class="carrito-content">
                <div class="productos-lista">
                    <?php if (empty($carrito)): ?>
                        <div class="carrito-vacio">
                            <p>Tu carrito está vacío</p>
                            <a href="index.php" class="btn-primary">Ir a la Tienda</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($carrito as $producto): ?>
                        <div id="producto-<?= $producto['id'] ?>" class="producto-item">
                            <img src="<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen del producto" width="100">

                            <div>
                                <h3><?= htmlspecialchars($producto['titulo']) ?></h3>
                                <p>Precio: $<?= number_format($producto['precio'], 2) ?></p>
                                <p>Descuento: $<?= number_format($producto['descuento'], 2) ?></p>
                                
                                <p class="subtotal-item" 
                                   data-id="<?= $producto['id'] ?>" 
                                   data-precio="<?= $producto['precio'] ?>" 
                                   data-descuento="<?= $producto['descuento'] ?>">
                                    Total: $<?= number_format($producto['total'], 2) ?>
                                </p>

                                <button class="btn-eliminar" data-id="<?= $producto['id'] ?>">Eliminar</button>
                            </div>
                        </div>
                        <hr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="resumen-pedido">
                    <h3>Resumen del Pedido</h3>
                    <div class="resumen-detalles">
                        <div class="resumen-item">
                            <span>Subtotal:</span>
                             <span id="subtotal">$<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="resumen-item">
                            <span>Descuentos:</span>
                            <span id="descuentos">$<?= number_format($descuentos, 2) ?></span>
                        </div>
                        <div class="resumen-total">
                            <span>Total:</span>
                            <span id="total">$<?= number_format($total, 2) ?></span>
                        </div>
                    </div>

                    <?php if (!empty($carrito)): ?>
                        <form action="metodoPago.php" method="POST">
                            <button type="submit" class="btn-primary">Proceder al Pago</button>
                        </form>
                    <?php endif; ?>

                    <a href="index.php" class="btn-secondary">Seguir Comprando</a>
                </div>
            </div>
        </div>  
    </main>
    
    <script src="carrito.js"></script>
</body>
</html>