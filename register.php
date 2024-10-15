<?php
include 'database.php';

//se define una nueva clase que contiene los metodos con sus propiedades para poder intercatcar 
class User {
    //se utiliza para almacenar una instancia de la clase db
    private $db;
    
    //se llama autmaticmante cuando se crea una nueva instancia de la claseeeeee y toma db como argumento y lo alamcena

    public function __construct($db) {
        $this->db = $db;



    }  
    //metodo que comprueba si un uaurio con el correo exise o no
    public function userExist($email, $username){
        //prepara la onsulta
        $stmt = $this->db->connection->prepare("SELECT * FROM Usuarios WHERE email = ? OR username = ?");

        //bind param enlza variables a los marcados que pasa como como argumentos

        //escibre una sentencia con marcadores de posicion como ?, y estos luego se enlazan con los valores 
        $stmt->bind_param("ss", $email, $username);
        //se ejecuta
        $stmt->execute();
        $stmt->store_result();         //devueleve true si se encontro algo y false ps nada

        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
    
    
    public function register($email, $username, $password) {
        try {
            
            // validacion de datos  con un filto especial 
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "El correo electronico no tiene un formato valido";
            }            if (strlen($username) < 5) {
                return "El nombre de usuario debe tener al menos 5 caracteres";
            }            if (strlen($password) < 8) {
                return "La contraseña debe tener al menos 8 caracteres";
                //verifica si ya paso por el meotod anterior, o sea que si e usuario ya existe o no
            }            if ($this->userExist($email, $username)){
                $stmt = $this->db->connection->prepare("SELECT * FROM Usuarios WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    return "El correo electronico ya existe";
                }                $stmt = $this->db->connection->prepare("SELECT * FROM Usuarios WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    return "El nombre de usuario ya existe";
                }            }     
                
                // si no existen inserta el nuevo usuario
                $hashed_password = password_hash($password, PASSWORD_DEFAULT); // hash a la contrasena
                $stmt = $this->db->connection->prepare("INSERT INTO Usuarios (email, username, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $username, $hashed_password); // alamecena ara usarlo en el login
            $stmt->execute();

            
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return "Usuario registrado exitosamente";
            } else {
                $stmt->close();
                return "Algo fallo en el registro del usuario";
            }        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }    }
}
$db = new db();
$user = new User($db);
$message = '';

if($_SERVER['REQUEST_METHOD']== 'POST'){

if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
    $result = $user->register($_POST['email'], $_POST['username'], $_POST['password']);
    if ($result === true) {
        $message = "Usuario registrado exitosamente";
    } else {
        $message = $result; //  mensaje de error
    }
} 
}
?>
 
<!DOCTYPE html>
<html>
 
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="logincss.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
 
<body>
    <div class="login-container">
        <h2>Registro</h2>
        
        <form action="" method="post">
        <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="username">Nombre de Usuario</label>
                <input type="text" id="username" name="username">
            </div>
            <div class="input-group">
                <label for="psw">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <input type="submit" value="Registrar" class="submit-btn">
            </div>

            <div class="links">
                
            <a href="login.php">       
             Inicia sesion aqui</a>
</div>
</div>
        </form>
    
</body>
       
    <script>

<?php if ($message != ''): ?>
    swal("<?php echo $message; ?>");
    <?php endif; ?>    
    
    </script>
</body>
 
</html>