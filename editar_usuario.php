<?php
include("conexion.php");
$id = $_GET['id'];
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $tipo = $_POST['tipo'];

    $stmt = $conexion->prepare("UPDATE Usuario SET nombre_usuario=?, email=?, tipo_usuario=? WHERE id_usuario=?");
    $stmt->bind_param("sssi", $nombre, $email, $tipo, $id);
    $stmt->execute();

    if ($stmt->execute()) {
        $mensaje = "Usario actualizado correctamente.";
    } else {
        $mensaje = "Error al editar: " . $conexion->error;
    }
}

$usuario = $conexion->query("SELECT * FROM Usuario WHERE id_usuario=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"> 
    <link rel="shortcut icon" type="image/png" href="imgs/logo.png?v=2">
    <title>Editar Usuario</title>
</head>
<body>
    <div class="admin-container">
        <div class="admin-section">
            
            <h2>Editar Usuario</h2>

            <?php if ($mensaje != ""): ?>
                <p class="message-success" style="color: #44ff00ff;"><strong><?= $mensaje ?></strong></p>
                <?php endif; ?>


            <div class="form-container">
                <form method="POST" onsubmit="return validarUsuarioEdicion(event);">
                    
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" id="nombre_u" value="<?= $usuario['nombre_usuario'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" id="email_u" value="<?= $usuario['email'] ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Tipo de usuario:</label>
                        <select name="tipo" id="tipo_u">
                            <option value="cliente" <?= $usuario['tipo_usuario'] == 'cliente' ? 'selected' : '' ?>>Cliente</option>
                            <option value="desarrollador" <?= $usuario['tipo_usuario'] == 'desarrollador' ? 'selected' : '' ?>>Desarrollador</option>
                            <option value="admin" <?= $usuario['tipo_usuario'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Actualizar</button>
                        <button type="button" class="btn-secondary" onclick="cancelar();">Terminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Navegación: vuelve al panel de usuarios si se cancela la edición
        function cancelar() {
            window.location.href = "admin.php#usuarios";
        } 
        
        // Validación de edición: lee inputs del DOM, valida formato (email) y redirige post-POST
        function validarUsuarioEdicion(event) {
            // Función auxiliar para obtener el nombre del campo a partir del ID
            function getFieldName(id) {
                const elemento = document.getElementById(id);
                return elemento ? elemento.previousElementSibling.textContent.replace(':', '').trim() : id;
            }

            const nombre = document.getElementById("nombre_u").value.trim();
            const correo = document.getElementById("email_u").value.trim();

            // 1. Validación de campos vacíos
            if (nombre === "") {
                alert(`El campo "${getFieldName('nombre_u')}" es obligatorio.`);
                return false;
            }

            if (correo === "") {
                alert(`El campo "${getFieldName('email_u')}" es obligatorio.`);
                return false;
            }

            // 2. Validación de formato de email (Expresión Regular)
            const RegExCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;        
            if (!(RegExCorreo.test(correo))) {
                alert('Dirección de correo electrónico inválida.');
                return false;
            }
            
            // 3. Redirección después de POST exitoso (esto se ejecuta solo si JS no retorna false)
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                window.location.href = "admin.php#usuarios";
                return false; // Previene el doble envío o errores si el header no se ejecuta
            <?php endif; ?>

            return true;
        }
        
        // Ejecuta la redirección si el script se recarga después de un POST exitoso
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            // Si la lógica PHP ya redirige con header(), este bloque es redundante,
            // pero lo dejamos como fallback si el header no se envía a tiempo.
            // Es mejor manejar la redirección en PHP si el HTML no se ha enviado.
            // Si el header() se ejecuta, el script se detiene y no llega aquí.
        <?php endif; ?>
    </script>
</body>
</html>