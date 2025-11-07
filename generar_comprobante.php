<?php
require('fpdf186/fpdf.php');
include("conexion.php");
session_start();

if (!isset($_GET['id_compra'])) {
    die("ID de compra no válido.");
}

$id_compra = intval($_GET['id_compra']);

// Obtener datos de la compra
$sql = "SELECT c.id_compra, c.fecha, c.total, c.metodo_pago, u.nombre_usuario, u.email 
        FROM compra c 
        LEFT JOIN usuario u ON c.id_usuario = u.id_usuario 
        WHERE c.id_compra = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_compra);
$stmt->execute();
$result = $stmt->get_result();
$compra = $result->fetch_assoc();
$stmt->close();

if (!$compra) {
    die("Compra no encontrada.");
}

// Obtener detalles
$sql_detalle = "SELECT v.titulo, d.cantidad, d.precio_unitario 
                FROM detallecompra d 
                JOIN videojuego v ON d.id_videojuego = v.id_videojuego
                WHERE d.id_compra = ?";
$stmt = $conexion->prepare($sql_detalle);
$stmt->bind_param("i", $id_compra);
$stmt->execute();
$detalles = $stmt->get_result();

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Comprobante de Compra - XTEAM GAMES',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','',12);

// Si no hay usuario, mostrar “Compra como invitado”
if (empty($compra['nombre_usuario'])) {
    $pdf->Cell(0,10,"Cliente: Compra realizada como invitado",0,1);
    $pdf->Cell(0,10,"Email (PayPal): " . utf8_decode($compra['email'] ?? 'No disponible'),0,1);
} else {
    $pdf->Cell(0,10,"Cliente: " . utf8_decode($compra['nombre_usuario']),0,1);
    $pdf->Cell(0,10,"Email: " . utf8_decode($compra['email']),0,1);
}

$pdf->Cell(0,10,"Fecha: " . $compra['fecha'],0,1);
$pdf->Cell(0,10,"Método de pago: " . $compra['metodo_pago'],0,1);
$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(100,10,'Videojuego',1);
$pdf->Cell(30,10,'Cantidad',1);
$pdf->Cell(40,10,'Precio Unitario',1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
while ($row = $detalles->fetch_assoc()) {
    $pdf->Cell(100,10,utf8_decode($row['titulo']),1);
    $pdf->Cell(30,10,$row['cantidad'],1);
    $pdf->Cell(40,10,'$'.number_format($row['precio_unitario'],2),1);
    $pdf->Ln();
}

$pdf->SetFont('Arial','B',12);
$pdf->Cell(130,10,'TOTAL',1);
$pdf->Cell(40,10,'$'.number_format($compra['total'],2),1,1,'R');

$pdf->Ln(15);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,10,'Gracias por tu compra en XTEAM GAMES.',0,1,'C');

// Guardar el archivo
$filename = "Comprobante_XTeam_" . $id_compra . ".pdf";
$filepath = __DIR__ . "/comprobantes/" . $filename;
$pdf->Output('F', $filepath); // Guarda el archivo

// Redirigir para abrir el PDF
header("Location: comprobantes/" . $filename);
exit;
?>
