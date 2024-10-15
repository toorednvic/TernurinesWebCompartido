<?php
class usuario {
    private $db;
 
    public function __construct($db) {
        $this->db = $db;
    }
 
    public function obtener($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->db->connection->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Error al preparar la consulta: ' . $this->db->connection->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id,$email, $username, $password, $role);
        $stmt->fetch();
 
        
 
        return array($id,$email, $username, $password, $role);
    }
 
    public function actualizar($id,$email, $username, $role) {
        $sql = "UPDATE usuarios SET email = ?, username = ?, role = ? WHERE id = ?";
        $stmt = $this->db->connection->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Error al preparar la consulta: ' . $this->db->connection->error);
        }    
        $stmt->bind_param("sssi",$email, $username, $role, $id);
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
$usuario = new usuario($db);
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'])) {
        echo "Error: no se proporcionó un ID.";
        exit;
    }
    try {
        echo $usuario->actualizar($_POST['id'],$_POST['email'], $_POST['username'], $_POST['role']);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else {
    if (!isset($_GET['id'])) {
        echo "Error: no se proporcionó un ID.";
        exit;
    }
    try {
        list($id,$email, $username, $role) = $usuario->obtener($_GET['id']);
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
<h1 class='text-center my-4'>Editar Usuario</h1>
<form method="POST">
<input type="hidden" name="id" value="<?php echo $id; ?>">

<div class="form-group"><div class = "row justify-content-center">
<h4>Id de Usuario:<?php echo $id; ?></h4>
</div>

<div class="form-group">
<label for="nombre">Usuario</label>
<input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
</div>
<div class="form-group">
<label for="nombre">email</label>
<input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
</div>

<div class="form-group">
<input type="hidden" class="form-control" id="password" name="password" value="<?php echo $password; ?>">
</div>
<div class="form-group">
<label for="apellido">Rol</label>
<input type="text" class="form-control" id="role" name="role" value="<?php echo $role; ?>">
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