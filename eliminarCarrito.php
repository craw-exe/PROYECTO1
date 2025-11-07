<?php
session_start();

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    if (isset($_SESSION['carrito'])) {
        // Filtra todos los productos excepto el que tiene el ID recibido
        $_SESSION['carrito'] = array_filter($_SESSION['carrito'], function ($item) use ($id) {
            return $item['id'] != $id;
        });
    }
}

// Redirigir de nuevo al carrito
header("Location: carrito.php");
exit;
?>
