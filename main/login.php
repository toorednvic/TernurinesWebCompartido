<?php
session_start(); 
 
include 'database.php';
 
class User {
    private $db;
 
    public function __construct($db) {
        $this->db = $db;
    }
 
    public function login($username, $password) {
        try {
            $stmt = $this->db->connection->prepare("SELECT * FROM Usuarios WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {

                
                $user = $result->fetch_assoc();
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['id_usuario'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['username'] =$username;
                    return true;
                } else {
                    return "Contraseña incorrecta para el usuario: " . $username;
                }
            } else {
                return "El nombre de usuario no existe: " . $username;
            }
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
 
$db = new db();
$user = new User($db);
 
if (isset($_POST['username']) && isset($_POST['password'])) {
    $result = $user->login($_POST['username'], $_POST['password']);
    if ($result === true) {
        if ($_SESSION['role'] == 'admin') {
            header('Location: Usuarios.php');
        } else {
            header('Location: home.php');
        }
        exit;
    } else {
        $_SESSION['message'] = $result; // guarda el mensaje de error en la sesión
    }
}
?>


<!DOCTYPE html>
<html>

<head>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<link rel="stylesheet" href="logincss.css">
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


</head>

<body>
<div class="login-container">
<h2>Inicia sesion</h2>


    <form action="" method="POST" class="container">
    <div class="input-group">
        <label for="uname"><b>Nombre de usuario</b></label>
        <input type="text" placeholder="Ingresa un nombre de usuario" name="username" required>
</div>
<div class="input-group">
        <label for="psw"><b>Contraseña</b></label>
        <input type="password" placeholder="Ingresa una contraseña" name="password" required>
</div>
<div class="input-group">
<input type="submit" value="Iniciar Sesión" class="submit-btn">
</div>
        <div class="links"> <a href="register.php ">Registrate aqui</a>
</div>
</div>
 </form>

</body>

<script>
<?php if (isset($_SESSION['message'])): ?>
    swal("<?php echo $_SESSION['message']; ?>");
<?php unset($_SESSION['message']); // borra el mensaje despues de mostrarlo ?>
<?php endif; ?>
</script>

</html>