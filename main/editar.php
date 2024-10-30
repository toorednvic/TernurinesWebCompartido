<?php
class Ternurin {
    private $db;
 
    public function __construct($db) {
        $this->db = $db;
    }
 
    public function obtener($id) {
        $sql = "SELECT * FROM Ternurines_Personalizados WHERE id = ?";
        $stmt = $this->db->connection->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Error al preparar la consulta: ' . $this->db->connection->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $id_usuario, $id_ternurin, $nombre, $apellido, $fecha_nacimiento, $genero, $estado_nacimiento);
        $stmt->fetch();
 
        // obtene la imagen y descripcion original
        $sql2 = "SELECT imagen, descripcion FROM Ternurin WHERE id = ?";
        $stmt2 = $this->db->connection->prepare($sql2);
        if ($stmt2 === false) {
            throw new Exception('Error al preparar la consulta: ' . $this->db->connection->error);
        }
        $stmt2->bind_param("i", $id_ternurin);
        $stmt2->execute();
        $stmt2->store_result();
        $stmt2->bind_result($imagen, $descripcion);
        $stmt2->fetch();
 
        return array($id, $id_usuario, $id_ternurin, $nombre, $apellido, $fecha_nacimiento, $genero, $estado_nacimiento, $imagen, $descripcion);
    }
 
    public function actualizar($id, $nombre, $apellido, $fecha_nacimiento, $genero, $estado_nacimiento) {
        $sql = "UPDATE Ternurines_Personalizados SET nombre = ?, apellido = ?, fecha_nacimiento = ?, genero = ?, estado_nacimiento = ? WHERE id = ?";
        $stmt = $this->db->connection->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Error al preparar la consulta: ' . $this->db->connection->error);
        }    
        $stmt->bind_param("sssssi", $nombre, $apellido, $fecha_nacimiento, $genero, $estado_nacimiento, $id);
        $stmt->execute();
        $stmt->store_result();
 
        if ($stmt->affected_rows > 0) {
            echo'<script type="text/javascript">
                alert("Los datos se actualizaron correctamente.");
                window.history.go(-2);
                        </script>';
        } else {
            echo'<script type="text/javascript">
                alert("No se pudo actualizar los datos. Revisa que el ID del ternurin es correcto.");
                window.history.go(-2);
                        </script>';
        }
    }
}
 
//  llama la clase
session_start();
include 'database.php';
$ternurin = new Ternurin($db);
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'])) {
        echo "Error: no se proporcionó un ID.";
        exit;
    }
    try {
        echo $ternurin->actualizar($_POST['id'], $_POST['nombre'], $_POST['apellido'], $_POST['fecha_nacimiento'], $_POST['genero'], $_POST['estado_nacimiento']);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else {
    if (!isset($_GET['id'])) {
        echo "Error: no se proporcionó un ID.";
        exit;
    }
    try {
        list($id, $id_usuario, $id_ternurin, $nombre, $apellido, $fecha_nacimiento, $genero, $estado_nacimiento, $imagen, $descripcion) = $ternurin->obtener($_GET['id']);
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
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
<h1 class='text-center my-4'>Editar Ternurin</h1>
<form method="POST">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<div class="form-group">
<div class = "row justify-content-center"><img src="<?php echo $imagen; ?>" alt="Imagen original del ternurin">
</div>
<div class="form-group"><div class = "row justify-content-center">
<p><?php echo $descripcion; ?></p>
</div>
<div class="form-group">
<label for="nombre">Nombre</label>
<input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>"required>
</div>
<div class="form-group">
<label for="apellido">Apellido</label>
<input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $apellido; ?>" required>
</div>
<div class="form-group">
<label for="fecha_nacimiento">Fecha de Nacimiento</label>
<input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento; ?>" required>
</div>
<div class="form-group">
<label for="genero">Género</label>
<input type="text" class="form-control" id="genero" name="genero" value="<?php echo $genero; ?>"required>
</div>
<div class="form-group">
<label for="estado_nacimiento">Estado de Nacimiento</label>
<input type="text" class="form-control" id="estado_nacimiento" name="estado_nacimiento" value="<?php echo $estado_nacimiento; ?>"required>
</div>
<button type="submit" class="btn btn-primary">Guardar</button>
</form>
</div>
<!-- JavaScript de Bootstrap -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
<?php
}
?>