<?php
require "conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre_usuario"];
    $email = $_POST["email"];
    $pass = $_POST["contraseña"];
    $tipo = $_POST["tipo_usuario"];

    // Generar hash
    $hash = password_hash($pass, PASSWORD_BCRYPT);

    $sql = "INSERT INTO Usuario (nombre_usuario, email, contraseña, tipo_usuario)
            VALUES (?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $email, $hash, $tipo);

    if ($stmt->execute()) {
        if ($tipo == "cliente") {
            $id_user = $stmt->insert_id;
            $conexion->query("INSERT INTO Cliente (id_usuario) VALUES ($id_user)");
            $mensaje = "Usuario agregado correctamente.";
        }

        if ($tipo == "desarrollador") {
            $id_user = $stmt->insert_id;
            $conexion->query("INSERT INTO Desarrollador (id_usuario) VALUES ($id_user)");
            $mensaje = "Usuario agregado correctamente.";
        }

    } else {
        $mensaje = "Error al registrar: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Registrar Usuario</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="admin-container">
            <div class="admin-section">
                <h2>Registrar nuevo usuario</h2>
                
                <?php if ($mensaje != ""): ?>
                <p class="message-success" style="color: #44ff00ff;"><strong><?= $mensaje ?></strong></p>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST" onsubmit="return validarRegistro(event);">
                        
                        <div class="form-group">
                            <label>Nombre de usuario:</label>
                            <input type="text" name="nombre_usuario" id="nombre_usuario">
                        </div>

                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" id="email">
                        </div>

                        <div class="form-group">
                            <label>Contraseña:</label>
                            <input type="password" name="contraseña" id="contraseña">
                        </div>

                        <div class="form-group">
                            <label>Tipo:</label>
                            <select name="tipo_usuario">
                                <option value="cliente">Cliente</option>
                                <option value="desarrollador">Desarrollador</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Guardar usuario</button>
                            <button type="button" class="btn-secondary" onclick="cancelar();">Terminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Navegación: redirige al panel de administración cuando el usuario cancela
            function cancelar() {
                window.location.href = "admin.php#usuarios"; 
            } 
            // Validación de form: obtiene valores desde el DOM (inputs) y valida antes de enviar
            function validarRegistro(event) {
                const nombreUsuario = document.getElementById("nombre_usuario").value.trim();
                const correo = document.getElementById("email").value.trim();
                const contrasena = document.getElementById("contraseña").value.trim();
                
                if (nombreUsuario === "") {
                    alert("Debes ingresar un nombre de usuario.");
                    return false;
                }

                if (correo === "") {
                    alert("Debes ingresar un correo electrónico.");
                    return false;
                }

                if (contrasena === "") {
                    alert("Debes ingresar una contraseña.");
                    return false;
                }

                if (contrasena.length < 6) {
                    alert("La contraseña debe tener al menos 6 caracteres.");
                    return false;
                }

                const RegExCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;        
                if (!(RegExCorreo.test(correo))) {
                    alert('Dirección de correo electrónico inválida.');
                    return false;
                }

                return true;
            }
        </script>
    </body>
</html>