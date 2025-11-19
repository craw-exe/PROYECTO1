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

    $stmt = $conexion->prepare("UPDATE videojuego SET titulo=?, descripcion=?, fecha_lanzamiento=?,
                                precio=?, url_imagen=?, id_desarrollador=?, requisitos_minimos=?,
                                requisitos_recomendados=?, plataforma=?, idiomas=?
                                WHERE id_videojuego=?");
    $stmt->bind_param("sssdsissssi", $titulo, $descripcion, $fecha_lanzamiento, $precio, $url_imagen,
                                    $id_desarrollador, $requisitos_minimos, $requisitos_recomendados,
                                    $plataforma, $idiomas, $id);
    $stmt->execute();

    header("Location: admin.php#usuarios");
    exit;
}

$datos = $conexion->query("SELECT * FROM videojuego WHERE id_videojuego=$id")->fetch_assoc();
?>
<form method="POST">
    <h2>Editar Videojuego</h2>
    <label>Titulo:</label><br>
    <input type="text" name="titulo" 
        value="<?= $datos['titulo'] ?>" required
        placeholder="Título del videojuego">
        <br><br>
    <label>Descripcion:</label><br>
    <textarea name="descripcion" required
        placeholder="Descripción del videojuego"><?= $datos['descripcion'] ?></textarea>
        <br><br>
    <label>Fecha de lanzamiento:</label><br>
    <input type="date" name="fecha_lanzamiento"
        value="<?= $datos['fecha_lanzamiento'] ?>" required>
        <br><br>
    <label>Precio:</label><br>
    <input type="number" step="0.01" name="precio"
        value="<?= $datos['precio'] ?>" required
        placeholder="Precio">
        <br><br>
    <label>URL de la imagen:</label><br>
    <input type="text" name="url_imagen"
        value="<?= $datos['url_imagen'] ?>" required
        placeholder="URL de la imagen">
    <label>Id del desarrollador:</label><br>
    <input type="number" name="id_desarrollador"
        value="<?= $datos['id_desarrollador'] ?>" required
        placeholder="ID del desarrollador">
        <br><br>
    <label>Requisitos minimos:</label><br>
    <textarea name="requisitos_minimos" required
        placeholder="Requisitos mínimos"><?= $datos['requisitos_minimos'] ?></textarea>
        <br><br>
    <label>Requisitos recomendados:</label><br>
    <textarea name="requisitos_recomendados" required
        placeholder="Requisitos recomendados"><?= $datos['requisitos_recomendados'] ?></textarea>
        <br><br>
    <label>Plataforma:</label><br>
    <input type="text" name="plataforma"
        value="<?= $datos['plataforma'] ?>" required
        placeholder="Plataforma (Ej: Windows, Linux...)">
        <br><br>
    <label>Idiomas:</label><br>
    <input type="text" name="idiomas"
        value="<?= $datos['idiomas'] ?>" required
        placeholder="Idiomas disponibles">
        <br><br>
    <button type="submit">Actualizar</button>
</form>
