<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_usuario = trim($_POST["nombre_usuario"]);
    $email = trim($_POST["email"]);
    $contraseña = $_POST["contraseña"];
    $confirm_password = $_POST["confirm_password"];

    // if (empty($nombre_usuario) || empty($email) || empty($contraseña) || empty($confirm_password)) {
    //     echo"<script>
    //                 alert('Todos los campos son obligatorios'); 
    //                 window.location.href='register.html';
    //             </script>";
    // }

    if ($contraseña !== $confirm_password) {
        echo"<script>
                    alert('Las contraseñas no coinciden.'); 
                    window.location.href='register.html';
                </script>";
    }

    // Verificar si el usuario o el correo ya existen
    $stmt = $conexion->prepare("SELECT id_usuario FROM Usuario WHERE nombre_usuario = ? OR email = ?");
    $stmt->bind_param("ss", $nombre_usuario, $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo"<script>
                    alert('El nombre de usuario o correo ya están registrados.'); 
                    window.location.href='login.html';
                </script>";
    }
    $stmt->close();

    // Hashear contraseña (bcrypt)
    $hash = password_hash($contraseña, PASSWORD_BCRYPT);

    // Llamar al procedimiento almacenado registrar_cliente
    $sql = "CALL registrar_cliente(?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $nombre_usuario, $email, $hash);

    if ($stmt->execute()) {
        echo"<script>
                    alert('Registro exitoso'); 
                    window.location.href='login.html';
                </script>";
    } else {
        echo "Error al registrar: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
} else {
    echo "Acceso no permitido.";
}
?>
