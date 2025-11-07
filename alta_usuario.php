<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $tipo = $_POST['tipo'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("INSERT INTO Usuario (nombre_usuario, email, tipo_usuario, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $tipo, $pass);
    $stmt->execute();

    header("Location: admin.php#usuarios");
    exit;
}
?>
<form method="POST">
    <h2>Agregar Usuario</h2>
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="email" name="email" placeholder="Correo" required>
    <select name="tipo">
        <option value="cliente">Cliente</option>
        <option value="desarrollador">Desarrollador</option>
        <option value="admin">Administrador</option>
    </select>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Guardar</button>
</form>