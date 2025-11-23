<?php
include("conexion.php");
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // La lógica de eliminación permanece aquí
    $stmt = $conexion->prepare("DELETE FROM Usuario WHERE id_usuario=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Nota: La redirección vía JS (alert/location.href) se realiza en el cliente.
    // La redirección HTTP (header) es mejor para scripts puros.
    // Usaremos la redirección al panel admin.
    
    // Si la eliminación fue exitosa, redirigir al panel
    if ($stmt->affected_rows > 0) {
        // Redirección silenciosa post-eliminación
        header("Location: admin.php#usuarios");
        exit;
    } else {
        // En caso de error, puedes manejarlo o redirigir
        header("Location: admin.php#usuarios");
        exit;
    }
}

$usuario = $conexion->query("SELECT * FROM Usuario WHERE id_usuario=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"> 
    <link rel="shortcut icon" type="image/png" href="imgs/logo.png?v=2">
    <title>Eliminar Usuario</title>
</head>
<body>
    <div class="admin-container">
        <div class="admin-section">
            
            <h2 style="color: #ff6b6b;">Confirmar Eliminación de Usuario</h2>

            <div class="form-container">
                <form method="POST">
                    
                    <div style="text-align: center; color: white; margin-bottom: 25px;">
                        ¿Estás seguro de que deseas eliminar el siguiente usuario?
                        <br>
                        <span style="color: #ff1500;">Esta acción no se puede deshacer.</span>
                    </div>

                    <div class="form-group">
                        <label>Nombre de usuario:</label>
                        <p class="data-display"><?= $usuario['nombre_usuario'] ?></p>
                    </div>
                    
                    <div class="form-group">
                        <label>Email:</label>
                        <p class="data-display"><?= $usuario['email'] ?></p>
                    </div>
                    
                    <div class="form-group">
                        <label>Tipo de usuario:</label>
                        <p class="data-display"><?= $usuario['tipo_usuario'] ?></p>
                    </div>
                    
                    <br>

                    <div class="form-actions">
                        <button type="submit" class="btn-delete">Eliminar</button> 
                        <button type="button" class="btn-secondary" onclick="cancelar();">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Navegación: cancelar vuelve al panel de usuarios
        function cancelar() {
            window.location.href = "admin.php#usuarios";
        } 
    </script>
</body>
</html>