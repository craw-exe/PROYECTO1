<?php
include("conexion.php");
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $tipo = $_POST['tipo'];

    $stmt = $conexion->prepare("UPDATE Usuario SET nombre_usuario=?, email=?, tipo_usuario=? WHERE id_usuario=?");
    $stmt->bind_param("sssi", $nombre, $email, $tipo, $id);
    $stmt->execute();

    header("Location: admin.php#usuarios");
    exit;
}

$usuario = $conexion->query("SELECT * FROM Usuario WHERE id_usuario=$id")->fetch_assoc();
?>
<form method="POST">
    <h2>Editar Usuario</h2>
    <input type="text" name="nombre" value="<?= $usuario['nombre_usuario'] ?>" required>
    <input type="email" name="email" value="<?= $usuario['email'] ?>" required>
    <select name="tipo">
        <option value="cliente" <?= $usuario['tipo_usuario'] == 'cliente' ? 'selected' : '' ?>>Cliente</option>
        <option value="desarrollador" <?= $usuario['tipo_usuario'] == 'desarrollador' ? 'selected' : '' ?>>Desarrollador</option>
        <option value="admin" <?= $usuario['tipo_usuario'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
    </select>
    <button type="submit">Actualizar</button>
</form>