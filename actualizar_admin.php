<?php
include("conexion.php");

// Contraseña real del admin
$password = 'maikAdmin123';
$tipo_usuario='admin';

// Generar hash
$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conexion->prepare("UPDATE Usuario SET 
                            contraseña=?, tipo_usuario=? 
                            WHERE nombre_usuario='maik'");
$stmt->bind_param("ss", $hash, $tipo_usuario);

if ($stmt->execute()) {
    echo "Contraseña del admin actualizada correctamente.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>