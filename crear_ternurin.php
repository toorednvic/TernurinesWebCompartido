<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION["id_usuario"])){
header("Location: login.php");

exit;

}

if ($_SESSION['role'] == '') {
    header('Location: home.php');
}   


include 'header.php';
include 'database.php';
class Ternurin {
    private $db;
 
    public function __construct($db) {
        $this->db = $db;
    }
 
    public function crearTernurin($nombre, $descripcion, $imagen) {
        try {
            $stmt = $this->db->connection->prepare("INSERT INTO Ternurin (nombre, descripcion, imagen) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $descripcion, $imagen);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                return true;
            } else {
                throw new Exception("No se pudo crear el Ternurin.");
            }
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage());
        }
    }
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new db();
    $ternurin = new Ternurin($db);
    try {
        // verifica si se cargo un archivo
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            // carga el archivo en el servidor
            $nombreArchivo = $_FILES['imagen']['name'];

            // en donde esta nuestro archivo
            $rutaTemporal = $_FILES['imagen']['tmp_name'];
            //finalmente estara nuestr a imagen
            $rutaDestino = 'C:\xampp\htdocs\TernurinesWeb' . $nombreArchivo;
            move_uploaded_file($rutaTemporal, $rutaDestino);
 
            // crea el ternurin con la ruta del archivo, servidor.
            $rutaRelativa = '/TernurinesWeb' . $nombreArchivo;
            $ternurin->crearTernurin($_POST['nombre'], $_POST['descripcion'], $rutaRelativa);
            echo '<script type="text/javascript">
                alert("El Ternurin se creo correctamente.");
                window.location.href = ""; 
                </script>';
        } else {
            throw new Exception("No se pudo cargar el archivo.");
        }    } catch (Exception $e) {
        echo $e->getMessage();
        echo '<script type="text/javascript">
            alert("'. addslashes($e->getMessage()) .'");
            window.location.href = ""; 
            </script>';
    }
} 
?>

<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS de Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class='text-center my-4'>Crear un nuevo Ternurin</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group"><label for="nombre">Nombre</label><input type="text" class="form-control" id="nombre" name="nombre"></div>
            <div class="form-group"><label for="descripcion">Descripción</label><textarea class="form-control" id="descripcion" name="descripcion"></textarea></div>
            <div class="form-group"><label for="imagen">URL de la imagen</label><input type="file" class="form-control" id="imagen" name="imagen"></div><button type="submit" class="btn btn-primary">Crear</button></form>
    </div>
    <!-- JavaScript de Bootstrap -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>