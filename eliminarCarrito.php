<?php
session_start();

// Verificamos que venga el ID y que exista el carrito
if (isset($_POST['id']) && isset($_SESSION['carrito'])) {
    $id_a_eliminar = $_POST['id'];
    $encontrado = false;

    // Recorremos el carrito para encontrar el índice correcto
    // Esto funciona tanto si tu array es asociativo como si es indexado
    foreach ($_SESSION['carrito'] as $indice => $producto) {
        if ($producto['id'] == $id_a_eliminar) {
            // Eliminamos el producto del array en sesión
            unset($_SESSION['carrito'][$indice]);
            $encontrado = true;
            break; // Dejamos de buscar una vez encontrado
        }
    }
    
    // Opcional: Reindexar el array para evitar huecos en los índices (0, 2, 3...)
    // Esto es útil si usas bucles 'for' tradicionales, aunque 'foreach' no tiene problema.
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);

    if ($encontrado) {
        echo "Producto eliminado correctamente";
    } else {
        echo "Error: Producto no encontrado en la sesión";
    }

} else {
    echo "Error: Datos inválidos";
}
?>