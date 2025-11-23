<?php
include("conexion.php");
$id = $_GET['id'];
$mensaje = "";

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

    if ($stmt->execute()) {
        $mensaje = "Juego actualizado correctamente.";
    } else {
        $mensaje = "Error al editar: " . $conexion->error;
    }
}

$datos = $conexion->query("SELECT * FROM videojuego WHERE id_videojuego=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"> 
    <title>Editar Videojuego</title>
</head>
<body>
    <div class="admin-container">
        <div class="admin-section">
            <h2>Editar Videojuego</h2>

            <?php if ($mensaje != ""): ?>
                <p class="message-success" style="color: #44ff00ff;"><strong><?= $mensaje ?></strong></p>
                <?php endif; ?>

            
            <div class="form-container">
                <form method="POST" onsubmit="return validarJuegoEdicion(event);">
                    
                    <div class="form-group">
                        <label>Título:</label>
                        <input type="text" name="titulo" id="titulo_e" 
                            value="<?= $datos['titulo'] ?>"
                            placeholder="Título del videojuego">
                    </div>

                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="descripcion" id="descripcion_e"
                            placeholder="Descripción del videojuego"><?= $datos['descripcion'] ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Fecha de lanzamiento:</label>
                        <input type="date" name="fecha_lanzamiento" id="fecha_lanzamiento_e"
                            value="<?= $datos['fecha_lanzamiento'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Precio:</label>
                        <input type="number" step="0.01" name="precio" id="precio_e"
                            value="<?= $datos['precio'] ?>"
                            placeholder="Precio">
                    </div>

                    <div class="form-group">
                        <label>URL de la imagen:</label>
                        <input type="text" name="url_imagen" id="url_imagen_e"
                            value="<?= $datos['url_imagen'] ?>"
                            placeholder="URL de la imagen">
                    </div>
                    
                    <div class="form-group">
                        <label>ID del desarrollador:</label>
                        <input type="number" name="id_desarrollador" id="id_desarrollador_e"
                            value="<?= $datos['id_desarrollador'] ?>"
                            placeholder="ID del desarrollador">
                    </div>
                    
                    <div class="form-group">
                        <label>Requisitos mínimos:</label>
                        <textarea name="requisitos_minimos" id="requisitos_minimos_e"
                            placeholder="Requisitos mínimos"><?= $datos['requisitos_minimos'] ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Requisitos recomendados:</label>
                        <textarea name="requisitos_recomendados" id="requisitos_recomendados_e"
                            placeholder="Requisitos recomendados"><?= $datos['requisitos_recomendados'] ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Plataforma:</label>
                        <input type="text" name="plataforma" id="plataforma_e"
                            value="<?= $datos['plataforma'] ?>"
                            placeholder="Plataforma (Ej: Windows, Linux...)">
                    </div>
                    
                    <div class="form-group">
                        <label>Idiomas:</label>
                        <input type="text" name="idiomas" id="idiomas_e"
                            value="<?= $datos['idiomas'] ?>"
                            placeholder="Idiomas disponibles">
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
        function cancelar() {
            window.location.href = "admin.php#videojuegos";
        } 
        
        function validarJuegoEdicion(event) {
            function getFieldName(id) {
                const elemento = document.getElementById(id);
                return elemento ? elemento.previousElementSibling.textContent.replace(':', '').trim() : id;
            }
            
            if (document.getElementById('titulo_e').value.trim() === "") {
                alert(`El campo "${getFieldName('titulo_e')}" es obligatorio.`);
                return false;
            }
            
            if (document.getElementById('descripcion_e').value.trim() === "") {
                alert(`El campo "${getFieldName('descripcion_e')}" es obligatorio.`);
                return false;
            }
            
            if (document.getElementById('fecha_lanzamiento_e').value.trim() === "") {
                alert(`El campo "${getFieldName('fecha_lanzamiento_e')}" es obligatorio.`);
                return false;
            }
            
            const precio = document.getElementById("precio_e").value;
            if (precio.trim() === "") {
                alert(`El campo "${getFieldName('precio_e')}" es obligatorio.`);
                return false;
            }

            if (document.getElementById('url_imagen_e').value.trim() === "") {
                alert(`El campo "${getFieldName('url_imagen_e')}" es obligatorio.`);
                return false;
            }
            
            if (document.getElementById('id_desarrollador_e').value.trim() === "") {
                alert(`El campo "${getFieldName('id_desarrollador_e')}" es obligatorio.`);
                return false;
            }
            
            if (document.getElementById('requisitos_minimos_e').value.trim() === "") {
                alert(`El campo "${getFieldName('requisitos_minimos_e')}" es obligatorio.`);
                return false;
            }
            
            if (document.getElementById('requisitos_recomendados_e').value.trim() === "") {
                alert(`El campo "${getFieldName('requisitos_recomendados_e')}" es obligatorio.`);
                return false;
            }
            
            if (document.getElementById('plataforma_e').value.trim() === "") {
                alert(`El campo "${getFieldName('plataforma_e')}" es obligatorio.`);
                return false;
            }
            
            if (document.getElementById('idiomas_e').value.trim() === "") {
                alert(`El campo "${getFieldName('idiomas_e')}" es obligatorio.`);
                return false;
            }
            
            const precioNumerico = parseFloat(precio);

            if (isNaN(precioNumerico) || precioNumerico < 0) {
                alert("El Precio debe ser un número válido (positivo o cero).");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>