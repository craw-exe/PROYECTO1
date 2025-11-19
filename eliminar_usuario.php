<?php
include("conexion.php");
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $tipo = $_POST['tipo'];

    $stmt = $conexion->prepare("DELETE FROM Usuario WHERE id_usuario=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo"<script>
                    alert('Usuario eliminado correctamente'); 
                    window.location.href='register.html';
                </script>";
    } else {
        echo"<script>
                    alert('No se encontró el usuario'); 
                    window.location.href='register.html';
                </script>";
    }

    header("Location: admin.php#usuarios");
    exit;
}

$usuario = $conexion->query("SELECT * FROM Usuario WHERE id_usuario=$id")->fetch_assoc();
?>
<form method="POST">
    <h2>Editar Usuario</h2>
    <label for="username">Nombre de usuario: <?= $usuario['nombre_usuario'] ?></label>
    <br>
    <label for="username">Email: <?= $usuario['email'] ?></label>
    <br>
    <label for="username">Tipo de usario: <?= $usuario['tipo_usuario'] ?></label>
    <br><br>
    <button type="submit">Eliminar</button>
</form>