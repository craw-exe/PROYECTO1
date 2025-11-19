<?php
include("conexion.php");
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_lanzamiento = $_POST['fecha_lanzamiento'];
    $precio = $_POST['precio'];
    $url_imagen = $_POST['url_imagen'];
    $id_desarrollador = $_POST['id_desarrollador'];
    $requisitos_minimos = $_POST['requisitos_minimos'];
    $requisitos_recomendados = $_POST['requisitos_recomendados'];
    $plataforma = $_POST['plataforma'];
    $idiomas = $_POST['idiomas'];

    $stmt = $conexion->prepare("DELETE videojuego WHERE id_videojuego=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo"<script>
                    alert('Videojuego eliminado correctamente');
                    window.location.href='register.html';
                </script>";
    } else {
        echo"<script>
                    alert('No se encontró el Videojuego');
                    window.location.href='register.html';
                </script>";
    }

    header("Location: admin.php#usuarios");
    exit;
}

$datos = $conexion->query("SELECT * FROM videojuego WHERE id_videojuego=$id")->fetch_assoc();
?>
<form method="POST">
    <h2>Editar Videojuego</h2>
    <label>Titulo: <?= $datos['titulo'] ?></label><br>
        <br><br>
    <label>Descripcion: <?= $datos['descripcion'] ?></label><br>
        <br><br>
    <label>Fecha de lanzamiento: <?= $datos['fecha_lanzamiento'] ?></label><br>
        <br><br>
    <label>Precio: <?= $datos['precio'] ?></label><br>
        <br><br>
    <label>URL de la imagen: <?= $datos['url_imagen'] ?></label><br>
        <br><br>
    <label>Id del desarrollador: <?= $datos['id_desarrollador'] ?></label><br>
        <br><br>
    <label>Requisitos minimos: <?= $datos['requisitos_minimos'] ?></label><br>
        <br><br>
    <label>Requisitos recomendados: <?= $datos['requisitos_recomendados'] ?></label><br>
        <br><br>
    <label>Plataforma: <?= $datos['plataforma'] ?></label><br>
        <br><br>
    <label>Idiomas: <?= $datos['idiomas'] ?></label><br>
        <br><br>
    <button type="submit">Eliminar</button>
</form>
