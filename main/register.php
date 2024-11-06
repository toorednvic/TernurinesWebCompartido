<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

include 'database.php';

//se define una nueva clase que contiene los metodos con sus propiedades para poder intercatcar 
class User {
    //se utiliza para almacenar una instancia de la clase db
    private $db;

    //se llama automáticamente cuando se crea una nueva instancia de la clase y toma db como argumento y lo almacena
    public function __construct($db) {
        $this->db = $db;
    }

    //metodo que comprueba si un usuario con el correo existe o no
    public function userExist($email, $username){
        //prepara la consulta
        $stmt = $this->db->connection->prepare("SELECT * FROM Usuarios WHERE email = ? OR username = ?");
        
        //bind param enlaza variables a los marcadores que pasa como argumentos
        $stmt->bind_param("ss", $email, $username);
        
        //se ejecuta
        $stmt->execute();
        $stmt->store_result(); //devuelve true si se encontró algo y false si no
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    //Método para registrar un nuevo usuario
    public function register($email, $username, $password) {
        try {
            // Validación de datos con un filtro especial
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    "success" => false,
                    "mensaje" => "El correo electronico no tiene un formato valido"
                ];
            }
            if (strlen($username) < 5) {
                return [
                    "success" => false,
                    "mensaje" => "El nombre de usuario debe tener al menos 5 caracteres"
                ];
            }
            if (strlen($password) < 8) {
                return [
                    "success" => false,
                    "mensaje" => "La contraseña debe tener al menos 8 caracteres"
                ];
            }            
            // Verifica si el usuario ya existe
            if ($this->userExist($email, $username)){
                $stmt = $this->db->connection->prepare("SELECT * FROM Usuarios WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    return [
                        "success" => false,
                        "mensaje" => "El correo electronico ya existe"
                    ];
                }                
                $stmt = $this->db->connection->prepare("SELECT * FROM Usuarios WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    return [
                        "success" => false,
                        "mensaje" => "El nombre de usuario ya existe"
                    ];
                }            
            }     

            // Si no existen, inserta el nuevo usuario
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hasheo de la contraseña

            // Se agregó la autenticación de dos factores
            $googleauth = new Google2FA();
            $codigo = $googleauth->generateSecretKey(); // Genera la clave secreta para 2FA
            
            // Se agregará la nueva columna para el código de autenticación en la base de datos
            $stmt = $this->db->connection->prepare("INSERT INTO Usuarios (email, username, password, codigoauth) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $email, $username, $hashed_password, $codigo); // Almacena la información del usuario
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $stmt->close();
                // Generación del código QR para la autenticación de dos factores
                $codigoQR = $googleauth->getQRCodeUrl('Ternurines', $email, $codigo);

                // Crear el renderizador y escritor de código QR
                $renderer = new ImageRenderer(
                    new RendererStyle(200),
                    new SvgImageBackEnd()
                );
                $writer = new Writer($renderer);
                
                // Genera el cOdigo QR en formato SVG
                $qrCodeSvg = $writer->writeString($codigoQR);

                // Convierte el cO  digo QR en base64 para usar en una imagen
                $qrCodeImage = base64_encode($qrCodeSvg);

                return [
                    "success" => true,
                    "codigoqr" => $qrCodeImage
                ];
            } else {
                $stmt->close();
                return [
                    "success" => false,
                    "mensaje" => "Algo fallo en el registro del usuario"
                ];
            }
        } catch (Exception $e) {
            return [
                "success" => false,
                "mensaje" => "Error: " . $e->getMessage()
            ];
        }
    }
}

$db = new db();
$user = new User($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
        $result = $user->register($_POST['email'], $_POST['username'], $_POST['password']);
        if ($result['success']) {
            $message = "Usuario registrado exitosamente";
            $codigoQRUrl = $result['codigoqr'];
        } else {
            $message = $result['mensaje'];
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
                <a href="login.php">Inicia sesión aquí</a>
            </div>
        </form>
        
        <?php if (isset($codigoQRUrl)): ?>
            <div>
                <h3>Código QR generado</h3>
                <img src="data:image/svg+xml;base64,<?php echo $codigoQRUrl; ?>" alt="QR" />
            </div>
        <?php endif; ?>
    </div>

    <script>
        <?php if ($message != ''): ?>
            swal("<?php echo $message; ?>");
        <?php endif; ?>    
    </script>
</body>
</html>
