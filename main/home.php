<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSS de Bootstrap -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<link href="cssTernures.css" rel="stylesheet">

<style>
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .card {
            flex: 1 1 calc(33.333% - 20px);
            box-sizing: border-box;
        }
</style>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION["id_usuario"])){
header("Location: login.php");

exit;

}


$id_usuario = $_SESSION["id_usuario"];
$username =$_SESSION["username"];

$role = isset($_SESSION["role"]) && !empty($_SESSION["role"]) ?
$_SESSION["role"] : 'invitado';





         include 'header.php';
?>
</head>
<body>

<button id="toggleButton">x</button>

<div class="iframe-container">
    <iframe src="baul.php" name="demo"></iframe>
</div> 
<script>
        const iframeContainer = document.querySelector('.iframe-container');
        const toggleButton = document.getElementById('toggleButton');

        toggleButton.addEventListener('click', () => {
        iframeContainer.classList.toggle('hidden');})
    </script>   

<div class="container text-center">


<div>
<h4> 
¡Bienvenido <?php echo$role; ?> <?php echo$username; ?>!</h4>

<h1>Elige tu ternurin favorito y personalizalo</h1>
<br>

<!-- <h4> ID de usuario: <?php echo $id_usuario; ?></h4> -->

<div class = "row justify-content-center">
<div class="card-container">

<?php
        include 'database.php';

        class Ternurin {
            private $id;
            private $nombre;
            private $descripcion;
            private $imagen;


            public function __construct($id, $nombre, $descripcion, $imagen) {
                $this->id = $id;
                $this->nombre = $nombre;
                $this->descripcion = $descripcion;
                $this->imagen = $imagen;
            }
           // metodos de cada propiedad 
        }
        try {


            $sql = "SELECT * FROM Ternurin";
            $resultado = $db->connection->query($sql);
            if ($resultado->num_rows > 0) {
                while($row = $resultado->fetch_assoc()) {
                    echo '<div class="card">';
                    echo '<img class="card-img-top" src="' . $row["imagen"] . '" alt="Imagen de ternurin">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . $row["nombre"] . '</h5>';
                    echo '<p class="card-text">' . $row["descripcion"] . '</p>';
                    echo '<form action="agregar_al_baul.php" method="POST">';
                    echo '<input type="hidden" name="id_ternurin" value="'.$row["id"] . '">';

                    if(isset($_SESSION["id_usuario"])){
                    echo '<input type="hidden" name="id_usuario" value="' .$_SESSION["id_usuario"] . '">';
                    }else {
                        
                    }                 
                       echo '<button type="submit" class="btn btn-primary">Agregar al baúl</button>';

                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No se encontraron ternurines";
            }
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }
        ?>
</div>
</div>
    </div>
<!-- JavaScript de Bootstrap -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>