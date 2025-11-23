<?php
include("conexion.php");
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conexion->prepare("DELETE FROM videojuego WHERE id_videojuego=?"); 
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin.php#videojuegos");
    exit;
}

$datos = $conexion->query("SELECT * FROM videojuego WHERE id_videojuego=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"> 
    <link rel="shortcut icon" type="image/png" href="imgs/logo.png?v=2">
    <title>Eliminar Videojuego</title>
</head>
<body>
    <div class="admin-container">
        <div class="admin-section">
            
            <h2 style="color: #ff6b6b;">Confirmar Eliminación de Videojuego</h2>

            <div class="form-container">
                <form method="POST">
                    
                    <div style="text-align: center; color: white; margin-bottom: 25px;">
                        Estás a punto de eliminar el siguiente videojuego. 
                        <br>
                        <span style="color: #ff1500;">Esta acción es irreversible. ¿Deseas continuar?.</span>
                    </div>

                    <div class="form-group">
                        <label>Título:</label>
                        <p class="data-display"><?= $datos['titulo'] ?></p>
                    </div>

                    <div class="form-group">
                        <label>Descripción:</label>
                        <p class="data-display"><?= $datos['descripcion'] ?></p>
                    </div>

                    <div class="form-group">
                        <label>Fecha de lanzamiento:</label>
                        <p class="data-display"><?= $datos['fecha_lanzamiento'] ?></p>
                    </div>

                    <div class="form-group">
                        <label>Precio:</label>
                        <p class="data-display"><?= $datos['precio'] ?></p>
                    </div>

                    <div class="form-group">
                        <label>URL de la imagen:</label>
                        <p class="data-display"><?= $datos['url_imagen'] ?></p>
                    </div>
                    
                    <div class="form-group">
                        <label>ID del desarrollador:</label>
                        <p class="data-display"><?= $datos['id_desarrollador'] ?></p>
                    </div>

                    <div class="form-group">
                        <label>Requisitos mínimos:</label>
                        <p class="data-display"><?= $datos['requisitos_minimos'] ?></p>
                    </div>

                    <div class="form-group">
                        <label>Requisitos recomendados:</label>
                        <p class="data-display"><?= $datos['requisitos_recomendados'] ?></p>
                    </div>

                    <div class="form-group">
                        <label>Plataforma:</label>
                        <p class="data-display"><?= $datos['plataforma'] ?></p>
                    </div>

                    <div class="form-group">
                        <label>Idiomas:</label>
                        <p class="data-display"><?= $datos['idiomas'] ?></p>
                    </div>
                    
                    <div class="form-actions" style="margin-top: 30px;">
                        <button type="submit" class="btn-delete">Eliminar</button>
                        <button type="button" class="btn-secondary" onclick="cancelar();">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Navegación: cancelar vuelve al panel de videojuegos (no hace modificaciones en el DOM)
        function cancelar() {
            window.location.href = "admin.php#videojuegos";
        } 
    </script>
</body>
</html>