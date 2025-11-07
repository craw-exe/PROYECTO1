<?php
session_start();

// Si no existe el carrito, se crea
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Verificar que se enviaron los datos
if (isset($_POST['id'], $_POST['titulo'], $_POST['precio'], $_POST['imagen'])) {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $precio = floatval($_POST['precio']);
    $imagen = $_POST['imagen'];
    $descuento = $_POST['descuento'];

    // Evitar duplicados
    $existe = false;
    foreach ($_SESSION['carrito'] as $producto) {
        if ($producto['id'] == $id) {
            $existe = true;
            break;
        }
    }

    if (!$existe) {
        $_SESSION['carrito'][] = [
            'id' => $id,
            'titulo' => $titulo,
            'precio' => $precio,
            'descuento' => $precio*$descuento/100,
            'imagen' => $imagen,
            'total' => $precio - ($precio*$descuento/100),
        ];
    }
}

// Redirigir al carrito
header("Location: carrito.php");
exit;
?>
