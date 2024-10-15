<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION["id_usuario"])){
header("Location: login.php");

exit;

}

$id_usuario =$_SESSION["id_usuario"];
$username =$_SESSION["username"];

$role = isset($_SESSION["role"]) && !empty($_SESSION["role"]) ?
$_SESSION["role"] : 'invitado';

if ($_SESSION['role'] == '') {
    header('Location: home.php');
}

include 'header.php';
include 'database.php';
class User {
    private $db;
 
    public function __construct($db) {
        $this->db = $db;
    }
 
    public function obtenerUsuarios() {
        try {
            $stmt = $this->db->connection->prepare("SELECT id, email, username, role FROM Usuarios");
            $stmt->execute();
            $result = $stmt->get_result();
$users = array();
while($user = $result->fetch_assoc()) {
    $users[] = $user;
}
return $users;

           
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
 
    public function obtenerUsuariosxTernurines() {
        try {
            $stmt = $this->db->connection->prepare("SELECT *FROM ternurines_personalizados");
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
 
$db = new db();
$user = new User($db);
$users = $user->obtenerUsuarios();

//$users = $user->getAllUsers();
$ternurines = $user->obtenerUsuariosxTernurines();
?>
<!DOCTYPE html>
<html>
<head>
<title>Inicio de Administrador</title>
<!--  CSS de Bootstrap -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
<h1 class="my-4">Inicio del Administrador</h1>
<h4> ¡Bienvenido <?php echo$role; ?> <?php echo$username; ?>!</h4>
<?php
        if ($users !== false) {
            echo "<h2>Usuarios</h2>";
            echo "<table class=\"table\">";
            echo "<thead class=\"thead-dark\"><tr><th>ID</th><th>Email</th><th>Username</th><th>Rol</th><th>Acciones</th></tr></thead>";
            echo "<tbody>";
            foreach ($users as $user) {
                echo "<tr><td>" . $user['id'] . "</td><td>" . $user['email'] . "</td><td>" . $user['username'] . "</td><td>" . $user['role'] . "</td>" ;
                echo '<td><form action="eliminar_admin.php" method="POST" onsubmit="return confirm(\'¿Estas seguro de que quieres eliminar este usuario?\');">';
                echo '<a href="editar_admin.php?id=' . $user["id"] . '" class="btn btn-secondary">Editar</a>';
                echo '<input type="hidden" name="id" value="' . $user["id"] . '">';
                echo '<button type="submit" class="btn btn-danger">Eliminar</button></td> </tr>';
                echo '</form>';
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No se pudo obtener la lista de usuarios.</p>";
        }
 
    if ($ternurines !== false) {
            echo "<h2>Ternurines</h2>";
            echo "<table class=\"table\">";
            echo "<thead class=\"thead-dark\"><tr><th>Id Usuario</th><th>Id Ternurin</th><th>Nombre de Ternurin</th><th>Apellido de Ternurinth></tr></thead>";
            echo "<tbody>";
            foreach ($ternurines as $ternurin) {
                echo "<tr><td>" . $ternurin['id_usuario'] . "</td><td>" . $ternurin['id_ternurin'] . "</td><td>" . $ternurin['nombre'] . "</td><td>" . $ternurin['apellido'] . "</td></tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No se pudo obtener la lista de ternurines.</p>";
        }
        ?>
</div>
<!-- Añade jQuery y los archivos JavaScript de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>