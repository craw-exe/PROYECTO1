<?php
session_start();
require_once '../fpdf186/fpdf.php';
include '../conexion.php';

$baseUrl = 'http://localhost/PROYECTO1';
$paypal_hostname = 'www.sandbox.paypal.com';
$pdt_identity_token = '5y8MgjHyQOvmdSyKOSg46ha4LPKDKfRoxnlnzBEUMBL63I8PQzAmAily1_e';

$tx = $_GET['tx'] ?? '';
if (!$tx) die("No se recibió la transacción.");

// Consulta PDT a PayPal
$query = "cmd=_notify-synch&tx=$tx&at=$pdt_identity_token";
$request = curl_init();
curl_setopt($request, CURLOPT_URL, "https://$paypal_hostname/cgi-bin/webscr");
curl_setopt($request, CURLOPT_POST, TRUE);
curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($request, CURLOPT_POSTFIELDS, $query);
$response = curl_exec($request);
curl_close($request);

if (!$response) die("Error al conectar con PayPal.");

$lines = explode("\n", trim($response));
$keyarray = [];

if (strcmp($lines[0], "SUCCESS") == 0) {
    for ($i = 1; $i < count($lines); $i++) {
        $temp = explode("=", $lines[$i], 2);
        $keyarray[urldecode($temp[0])] = urldecode($temp[1]);
    }

    $payment_status = $keyarray['payment_status'] ?? '';
    $mc_gross = $keyarray['mc_gross'] ?? 0;

    if ($payment_status === 'Completed') {

        // Determinar usuario
        if (isset($_SESSION['usuario'])) {
            $nombre_usuario = $_SESSION['usuario'];
            $stmt_usr = $conexion->prepare("SELECT id_usuario FROM Usuario WHERE nombre_usuario = ?");
            $stmt_usr->bind_param("s", $nombre_usuario);
            $stmt_usr->execute();
            $res = $stmt_usr->get_result();
            $usuario_data = $res->fetch_assoc();
            $id_usuario = $usuario_data['id_usuario'];
            $stmt_usr->close();
        } elseif (isset($_SESSION['usuario_invitado'])) {
            $id_usuario = $_SESSION['usuario_invitado'];
        } else {
            // Crear usuario invitado
            $nombre_invitado = 'Invitado_' . time();
            $email_invitado = 'invitado_' . time() . '@xteam.local';
            $password_dummy = bin2hex(random_bytes(8));

            $stmt = $conexion->prepare("INSERT INTO Usuario (nombre_usuario, email, contraseña, tipo_usuario) VALUES (?, ?, ?, 'cliente')");
            $stmt->bind_param("sss", $nombre_invitado, $email_invitado, $password_dummy);
            $stmt->execute();
            $id_usuario = $stmt->insert_id;
            $stmt->close();

            $stmt = $conexion->prepare("INSERT INTO Cliente (id_usuario) VALUES (?)");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $stmt->close();

            $_SESSION['usuario_invitado'] = $id_usuario;
        }

        // Tomar items del carrito o compra directa
        $carrito = $_SESSION['carrito'] ?? [];
        if (empty($carrito) && isset($keyarray['item_name1'])) {
            $carrito[] = [
                'id' => $keyarray['item_number1'] ?? 0,
                'titulo' => $keyarray['item_name1'] ?? '',
                'precio' => $keyarray['mc_gross'] ?? 0,
                'cantidad' => $keyarray['quantity1'] ?? 1
            ];
        }

        // Insertar Compra
        $stmt = $conexion->prepare("INSERT INTO Compra (id_usuario, total, metodo_pago, estado) VALUES (?, ?, 'paypal', 'completada')");
        $stmt->bind_param("id", $id_usuario, $mc_gross);
        $stmt->execute();
        $id_compra = $stmt->insert_id;
        $stmt->close();

        // Insertar DetalleCompra y Biblioteca
        $stmt_detalle = $conexion->prepare("INSERT INTO DetalleCompra (id_compra, id_videojuego, precio_unitario) VALUES (?, ?, ?)");
        $stmt_biblio = $conexion->prepare("INSERT IGNORE INTO Biblioteca (id_usuario, id_videojuego) VALUES (?, ?)");

        foreach ($carrito as $item) {
            $stmt_detalle->bind_param("iid", $id_compra, $item['id'], $item['precio']);
            $stmt_detalle->execute();

            // Biblioteca
            $stmt_biblio->bind_param("ii", $id_usuario, $item['id']);
            $stmt_biblio->execute();
        }
        $stmt_detalle->close();
        $stmt_biblio->close();

        // Generar PDF
        class PDF extends FPDF {
            function Header() {
                $logoPath = __DIR__ . '/../img/logo.png';
                if(file_exists($logoPath)) $this->Image($logoPath, 10, 8, 30);
                $this->SetFont('Arial','B',15);
                $this->Cell(0,10,'Comprobante de Compra',0,1,'C');
                $this->Ln(10);
            }
        }

        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','',12);

        $pdf->Cell(0,8,'Compra ID: '.$id_compra,0,1);
        $pdf->Cell(0,8,'Usuario: '.($id_usuario ?? 'Invitado'),0,1);
        $pdf->Cell(0,8,'Fecha: '.date('Y-m-d H:i:s'),0,1);
        $pdf->Cell(0,8,'Metodo de pago: PayPal',0,1);
        $pdf->Ln(5);
        $pdf->Cell(0,8,'Detalle de productos:',0,1);

        foreach ($carrito as $item) {
            $pdf->Cell(0,8,"- {$item['titulo']} | Cantidad: {$item['cantidad']} | Precio unitario: $".number_format($item['precio'],2),0,1);
        }
        $pdf->Ln(5);
        $pdf->Cell(0,8,'Total: $'.number_format($mc_gross,2),0,1);

        // Guardar PDF en carpeta comprobantes
        $dir = __DIR__ . '/../comprobantes/';
        if(!is_dir($dir)) mkdir($dir, 0777, true);
        $filename = $dir . "comprobante_{$id_compra}.pdf";
        $pdf->Output('F', $filename);

        // Registrar Recibo
        $stmt_recibo = $conexion->prepare("INSERT INTO Recibo (id_compra, nombre_archivo, contenido) VALUES (?, ?, ?)");
        $pdf_content = file_get_contents($filename);
        $stmt_recibo->bind_param("iss", $id_compra, $filename, $pdf_content);
        $stmt_recibo->execute();
        $stmt_recibo->close();

        // Abrir PDF
        header('Content-Type: application/pdf');
        header("Content-Disposition: inline; filename=comprobante_{$id_compra}.pdf");
        readfile($filename);
        header('Location: ../biblioteca.php');
        exit;

    } else {
        die("El pago no se completó. Estado: $payment_status");
    }
} else {
    die("FAIL en la verificación de PayPal.");
}
