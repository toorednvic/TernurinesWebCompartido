<?php
require('fpdf/fpdf.php');
 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}
 
include 'database.php';
 
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('REPUBLICA DE LOS TERNURINES UNIDOS'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Ln(10);
    }
 
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
    }
 
    function DrawFrame() {
        $this->SetDrawColor(0, 128, 255);
        $this->SetLineWidth(1);
        $this->Rect(5, 5, 200, 287, 'D');
    }
 
    function AddSeal($x, $y, $sealPath) {
        $this->Image($sealPath, $x, $y, 40); 
    }
}
 
function generarCURP($nombre, $apellido, $fecha_nacimiento, $genero, $estado_nacimiento) {
    return strtoupper(substr($apellido, 0, 2) . substr($nombre, 0, 2) . substr($fecha_nacimiento, 2, 2) . $genero . substr($estado_nacimiento, 0, 2));
}
 
function generarFolio() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
}
 
if (isset($_GET['id'])) {
    $id = $_GET['id'];
 
    $sql = "SELECT Ternurines_Personalizados.*, Ternurin.imagen FROM Ternurines_Personalizados JOIN Ternurin ON Ternurines_Personalizados.id_ternurin = Ternurin.id WHERE Ternurines_Personalizados.id = ?";
    $stmt = $db->connection->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $row = $resultado->fetch_assoc();
 
    if ($row) {
        $nombre = $row['nombre'];
        $apellido = $row['apellido'];
        $fecha_nacimiento = $row['fecha_nacimiento'];
        $genero = $row['genero'];
        $estado_nacimiento = $row['estado_nacimiento'];
        $imagen = $row['imagen'];
 
        // ruta de la imagen desde 000
        $imagen = $_SERVER['DOCUMENT_ROOT'] . $imagen;
        $ruta_absoluta = realpath($imagen);
 
        if (!empty($ruta_absoluta) && file_exists($ruta_absoluta)) {
            $curp = generarCURP($nombre, $apellido, $fecha_nacimiento, $genero, $estado_nacimiento);
            $folio = generarFolio();
            $fecha_hoy = date('d/m/Y');
 
            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->DrawFrame();
 
            // Encabezado
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(240, 10, utf8_decode('Certificado de Registro Único de Ternurines'), 0, 1, 'C');
            $pdf->Ln(8);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(75, 10, 'Clave:', 0, 0, 'R');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(60, 10, $curp, 0, 1, 'L');
            $pdf->Ln(8);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(79, 10, 'Nombre:', 0, 0, 'R');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 10, $nombre, 0, 1, 'L');
            $pdf->Ln(1);
 
            $pdf->Image($ruta_absoluta, 10, 30, 50); // hacer imagen mas grande
            $pdf->Ln(8); // espacio entre componentes
 
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'TERNURINES WEB', 0, 1, 'C');
            $pdf->Ln(10);
 
            // datos del ternernurin
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(40, 10, 'Fecha de Emision:', 30, 0, 'R');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 10, $fecha_hoy, 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(80, -10, 'Folio:', 0, 0, 'R');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, -10, $folio, 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(160, 10, 'Entidad de Registro:', 0, 0, 'R');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 10, $estado_nacimiento, 0, 1, 'L');
 
            // sello
            $pdf->AddSeal(150, 45, 'imgSellojpg'); 
 
            $pdf->Output();
        } else {
            echo "La imagen no existe o la ruta es incorrecta.";
        }
    } else {
        echo "No se encontraron datos para el ID proporcionado.";
    }
} else {
    echo "ID no proporcionado.";
}
?>