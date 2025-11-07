<?php
session_start();

$baseUrl = 'http://localhost/PROYECTO1'; // Ajusta a tu URL base

// Verificar si es una compra directa (datos POST)
$carrito = [];
$total = 0;
$totald = 0;

if (isset($_POST['compra_directa']) && $_POST['compra_directa'] == '1') {
    // Crear un carrito temporal con el ítem de compra directa
    $carrito[] = [
        'id' => $_POST['id_videojuego'],
        'titulo' => $_POST['titulo'],
        'precio' => $_POST['precio'],
        'imagen' => $_POST['imagen'],
        'cantidad' => 1, // Asumir cantidad 1 para compra directa
        'total' => $_POST['precio'], // Total por ítem (precio * cantidad)
        'descuento' => 0 // Sin descuento por defecto
    ];
    $total = $_POST['precio'];
} else {
    // Usar el carrito de la sesión si existe
    $carrito = $_SESSION['carrito'] ?? [];
    foreach ($carrito as $item) {
        $total += $item['total'];
        $totald += $item['descuento'];
    }
}

// Verificar si el usuario está logueado y obtener créditos (asumiendo $_SESSION['usuario'] con 'creditos')
$usuario_logueado = isset($_SESSION['usuario']);
$creditos_disponibles = $usuario_logueado ? ($_SESSION['usuario']['creditos'] ?? 0) : 0;

// Variables para control del modal y mensajes de error
$mostrar_modal = false;
$mensaje_error = '';

// Determinar la action del formulario basado en el método seleccionado
$action = 'metodoPago.php'; // Por defecto, para Créditos
$metodo_pago = $_POST['payment-method'] ?? '';
if ($metodo_pago === 'paypal') {
    $action = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
}

// Procesar el pago si se envió el formulario
if (isset($_POST['procesar_pago'])) {
    if ($metodo_pago === 'creditos') {
        if (!$usuario_logueado) {
            $mensaje_error = 'Debes iniciar sesión para pagar con créditos.';
        } elseif ($creditos_disponibles < $total) {
            $mensaje_error = 'No tienes suficientes créditos para completar la compra.';
        } else {
            // Simular procesamiento con créditos: Restar de la sesión (en producción, actualiza BD)
            $_SESSION['usuario']['creditos'] -= $total;
            $mostrar_modal = true; // Mostrar modal de procesamiento
        }
    } elseif ($metodo_pago === 'paypal') {
        // Para PayPal, el formulario se enviará directamente a PayPal (no procesar aquí)
        // Los datos se pasan en el form HTML
    } else {
        $mensaje_error = 'Selecciona un método de pago válido.';
    }

    // Si no hay error y se mostró modal (solo para Créditos), redirigir
    if ($mostrar_modal && empty($mensaje_error)) {
        header('Location: confirmacion.php'); // Redirigir a página de confirmación
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Método de Pago - Xteam</title>
    <link rel="stylesheet" href="stylePago.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="index.html" class="logo">Xteam</a>
            <ul class="nav-links">
                <li><a href="index.php">Tienda</a></li>
                <li><a href="biblioteca.html">Biblioteca</a></li>
                <li><a href="carrito.php">Carrito</a></li>
                <li><a href="reviews.html">Reseñas</a></li>
                <li><a href="nosotros.html">Acerca de</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <div class="checkout-container">
            <h1>Selecciona Método de Pago</h1>
            
            <div class="checkout-steps">
                <div class="step active">Carrito</div>
                <div class="step active">Pago</div>
                <div class="step">Confirmación</div>
            </div>

            <!-- Mostrar mensaje de error si existe -->
            <?php if (!empty($mensaje_error)): ?>
                <div class="error-message" style="color: red; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($mensaje_error); ?>
                </div>
            <?php endif; ?>

            <!-- Formulario para métodos de pago -->
            <form action="<?php echo htmlspecialchars($action); ?>" method="POST">
                <!-- Campos para PayPal (solo si se selecciona PayPal) -->
                <?php if ($metodo_pago === 'paypal'): ?>
                    <input type="hidden" name="business" value="sb-uw1p647359526@business.example.com">
                    <input type="hidden" name="cmd" value="_cart">
                    <input type="hidden" name="upload" value="1">
                    <input type="hidden" name="currency_code" value="MXN">
                    <input type="hidden" name="return" value="<?php echo htmlspecialchars($baseUrl); ?>/pp/receptor.php">
                    <input type="hidden" name="cancel_return" value="<?php echo htmlspecialchars($baseUrl); ?>/pp/pago_cancelado.php">
                    
                    <!-- items -->
                    <?php $i = 1; foreach ($carrito as $item): ?>
                        <input type="hidden" name="item_name_<?php echo $i; ?>" value="<?php echo htmlspecialchars($item['titulo']); ?>">
                        <input type="hidden" name="amount_<?php echo $i; ?>" value="<?php echo number_format($item['precio'], 2, '.', ''); ?>">
                        <input type="hidden" name="quantity_<?php echo $i; ?>" value="<?php echo 1; ?>">
                        <?php $i++; endforeach; ?>
                <?php endif; ?>

                <!-- Pasar datos del carrito si es compra directa (para mantener estado) -->
                <?php if (isset($_POST['compra_directa'])): ?>
                    <input type="hidden" name="compra_directa" value="1">
                    <input type="hidden" name="id_videojuego" value="<?php echo htmlspecialchars($_POST['id_videojuego']); ?>">
                    <input type="hidden" name="titulo" value="<?php echo htmlspecialchars($_POST['titulo']); ?>">
                    <input type="hidden" name="precio" value="<?php echo htmlspecialchars($_POST['precio']); ?>">
                    <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($_POST['imagen']); ?>">
                <?php endif; ?>

                <div class="payment-methods">
                    <!-- Método: Créditos (solo para usuarios registrados) -->
                    <div class="payment-method" id="method-creditos" style="display: <?php echo $usuario_logueado ? 'block' : 'none'; ?>;">
                        <input type="radio" name="payment-method" id="creditos" value="creditos" <?php echo ($usuario_logueado && !isset($_POST['payment-method'])) ? 'checked' : ''; ?>>
                        <label for="creditos">
                            <div class="method-info">
                                <h3>Pagar con Créditos</h3>
                                <p id="creditos-disponibles">Créditos disponibles: $<?php echo number_format($creditos_disponibles, 2); ?></p>
                            </div>
                        </label>
                    </div>

                    <!-- Método: PayPal -->
                    <div class="payment-method">
                        <input type="radio" name="payment-method" id="paypal" value="paypal" <?php echo (!$usuario_logueado || isset($_POST['payment-method']) && $_POST['payment-method'] === 'paypal') ? 'checked' : ''; ?>>
                        <label for="paypal">
                            <div class="method-info">
                                <h3>PayPal</h3>
                                <p>Paga rápido y seguro con tu cuenta PayPal</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="order-summary">
                    <h3>Resumen de Compra</h3>
                    <div id="resumen-productos">
                        <?php if (!empty($carrito)): ?>
                            <ul>
                                <?php foreach ($carrito as $item): ?>
                                    <li><?= htmlspecialchars($item['titulo']) ?> : $<?= number_format($item['total'], 2) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No hay productos en el carrito.</p>
                        <?php endif; ?>
                    </div>
                    <div class="order-total">
                        <strong>Total: <span id="total-compra">$<?= number_format($total, 2) ?></span></strong>
                    </div>
                    <?php if($totald > 0){
                        echo '<div class="order-total">
                                <strong>Usted Ahorro: <span id="total-compra">$'.number_format($totald, 2).'</span></strong>
                            </div>';
                    }?>
                </div>

                <div class="checkout-actions">
                    <button type="submit" name="procesar_pago" value="1" class="btn-primary">
                        Proceder al Pago
                    </button>
                    <a href="carrito.php" class="btn-secondary">Volver al Carrito</a>
                </div>
            </form>
        </div>
    </main>

    <!-- Modal de procesamiento (mostrado con PHP si $mostrar_modal es true) -->
    <div id="processing-modal" class="modal" style="display: <?php echo $mostrar_modal ? 'block' : 'none'; ?>;">
        <div class="modal-content">
            <div class="processing-spinner"></div>
            <h3>Procesando tu pago...</h3>
            <p>Por favor no cierres esta ventana.</p>
        </div>
    </div>
</body>
</html>