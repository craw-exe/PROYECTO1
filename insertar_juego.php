<?php
require "conexion.php";

$devs = $conexion->query("SELECT U.id_usuario, U.nombre_usuario FROM Usuario U WHERE tipo_usuario = 'desarrollador'");
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo      = $_POST["titulo"];
    $descripcion = $_POST["descripcion"];
    $fecha       = $_POST["fecha_lanzamiento"];
    $precio      = $_POST["precio"];
    $imagen      = $_POST["url_imagen"];
    $dev         = $_POST["id_desarrollador"];
    $req_min     = $_POST["requisitos_minimos"];
    $req_rec     = $_POST["requisitos_recomendados"];
    $plataforma  = $_POST["plataforma"];
    $idiomas     = $_POST["idiomas"];

    $sql = "INSERT INTO Videojuego (titulo, descripcion, fecha_lanzamiento, precio, url_imagen, id_desarrollador, requisitos_minimos, requisitos_recomendados, plataforma, idiomas)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssdsissss", $titulo, $descripcion, $fecha, $precio, $imagen, $dev, $req_min, $req_rec, $plataforma, $idiomas);
    if ($stmt->execute()) {
        $mensaje = "Juego agregado correctamente.";
    } else {
        $mensaje = "Error al insertar: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Insertar Videojuego</title>
        <link rel="stylesheet" href="style.css"> 
        <link rel="shortcut icon" type="image/png" href="imgs/logo.png?v=2">
    </head>
    <body>
        <div class="admin-container">
            <div class="admin-section">
                
                <h2>Registrar nuevo videojuego</h2>

                <?php if ($mensaje != ""): ?>
                <p class="message-success" style="color: #44ff00ff;"><strong><?= $mensaje ?></strong></p>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST" onsubmit="return validarJuego(event);">
                        
                        <div class="form-group">
                            <label>Título:</label>
                            <input type="text" name="titulo" id="titulo">
                        </div>

                        <div class="form-group">
                            <label>Descripción:</label>
                            <textarea name="descripcion" id="descripcion" rows="5"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Fecha de lanzamiento:</label>
                            <input type="date" name="fecha_lanzamiento" id="fecha_lanzamiento">
                        </div>

                        <div class="form-group">
                            <label>Precio:</label>
                            <input type="number" step="0.01" name="precio" id="precio">
                        </div>

                        <div class="form-group">
                            <label>URL de imagen:</label>
                            <input type="text" name="url_imagen" id="url_imagen">
                        </div>

                        <div class="form-group">
                            <label>Desarrollador:</label>
                            <select name="id_desarrollador" id="id_desarrollador">
                                <option value="">Seleccionar...</option>
                                <?php while($d = $devs->fetch_assoc()): ?>
                                    <option value="<?= $d['id_usuario'] ?>"><?= $d['nombre_usuario'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Requisitos mínimos:</label>
                            <textarea name="requisitos_minimos" id="requisitos_minimos" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Requisitos recomendados:</label>
                            <textarea name="requisitos_recomendados" id="requisitos_recomendados" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Plataforma:</label>
                            <input type="text" name="plataforma" id="plataforma">
                        </div>

                        <div class="form-group">
                            <label>Idiomas:</label>
                            <input type="text" name="idiomas" id="idiomas">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Guardar videojuego</button>
                            <button type="button" class="btn-secondary" onclick="cancelar();">Terminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Navegación: redirige al listado de videojuegos en el panel admin
            function cancelar() {
                window.location.href = "admin.php#videojuegos";
            } 
            
            // Validación de formulario: lee campos del DOM, valida vacíos y formato (precio)
            function validarJuego(event) {
                // Función auxiliar para obtener el nombre del campo a partir del ID
                function getFieldName(id) {
                    const elemento = document.getElementById(id);
                    return elemento ? elemento.previousElementSibling.textContent.replace(':', '').trim() : id;
                }

                // VALIDACIÓN DE CAMPOS VACÍOS (IFs INDEPENDIENTES)
                
                // 1. Título
                if (document.getElementById('titulo').value.trim() === "") {
                    alert(`El campo "${getFieldName('titulo')}" es obligatorio.`);
                    return false;
                }
                
                // 2. Descripción
                if (document.getElementById('descripcion').value.trim() === "") {
                    alert(`El campo "${getFieldName('descripcion')}" es obligatorio.`);
                    return false;
                }
                
                // 3. Fecha de lanzamiento
                if (document.getElementById('fecha_lanzamiento').value.trim() === "") {
                    alert(`El campo "${getFieldName('fecha_lanzamiento')}" es obligatorio.`);
                    return false;
                }
                
                // 4. Precio
                const precio = document.getElementById("precio").value;
                if (precio.trim() === "") {
                    alert(`El campo "${getFieldName('precio')}" es obligatorio.`);
                    return false;
                }

                // 5. URL de imagen
                if (document.getElementById('url_imagen').value.trim() === "") {
                    alert(`El campo "${getFieldName('url_imagen')}" es obligatorio.`);
                    return false;
                }
                
                // 6. Desarrollador (Select)
                if (document.getElementById('id_desarrollador').value === "") {
                    alert(`El campo "${getFieldName('id_desarrollador')}" es obligatorio.`);
                    return false;
                }
                
                // 7. Requisitos mínimos
                if (document.getElementById('requisitos_minimos').value.trim() === "") {
                    alert(`El campo "${getFieldName('requisitos_minimos')}" es obligatorio.`);
                    return false;
                }
                
                // 8. Requisitos recomendados
                if (document.getElementById('requisitos_recomendados').value.trim() === "") {
                    alert(`El campo "${getFieldName('requisitos_recomendados')}" es obligatorio.`);
                    return false;
                }
                
                // 9. Plataforma
                if (document.getElementById('plataforma').value.trim() === "") {
                    alert(`El campo "${getFieldName('plataforma')}" es obligatorio.`);
                    return false;
                }
                
                // 10. Idiomas
                if (document.getElementById('idiomas').value.trim() === "") {
                    alert(`El campo "${getFieldName('idiomas')}" es obligatorio.`);
                    return false;
                }

                // VALIDACIÓN DE FORMATO (Precio)
                
                const precioNumerico = parseFloat(precio);

                if (isNaN(precioNumerico) || precioNumerico < 0) {
                    alert("El Precio debe ser un número válido (positivo o cero).");
                    return false;
                }

                // Si todas las validaciones pasan
                return true;
            }
        </script>
    </body>
</html>