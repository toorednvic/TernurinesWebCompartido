<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION["id_usuario"])){
header("Location: login.php");

exit;

}
         include 'header.php';
         include 'database.php';
         
         class Ternurin {
            
    private $db;
 
    public function __construct($db) {
        $this->db = $db;
    }
     public function obtenerTodos($id_usuario) {
        try {
            $sql = "SELECT Ternurines_Personalizados.*, Ternurin.imagen, Ternurin.descripcion FROM Ternurines_Personalizados
            JOIN Ternurin ON Ternurines_Personalizados.id_ternurin = Ternurin.id WHERE Ternurines_Personalizados.id_usuario = ?";
            $stmt = $this->db->connection->prepare($sql);
            if ($stmt === false) {
                throw new Exception('Error al preparar la consulta: ' . $this->db->connection->error);
            }            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $resultado = $stmt->get_result();
 
            
            $ternurines = array();
            while($row = $resultado->fetch_assoc()) {
                $ternurines[] = $row;
            }            return $ternurines;
        } catch (Exception $e) {
            return $e->getMessage();
        }    }
}
 
 $ternurin = new Ternurin($db);
 $ternurines = array(); // Inicializa $ternurines como un array vacioo

 if(isset($_SESSION['id_usuario'])){
    $ternurines = $ternurin->obtenerTodos($_SESSION['id_usuario']);
}else {
    echo "error";

}


?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSS de Bootstrap -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
 
<style>
 
.card {
            display:block; /* Inicialmente oculto */         
               margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;        
                border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);

        }        .card-container {         
           display: flex;            
           flex-wrap: wrap;       
                justify-content: center;  
                          gap: 20px;
        }      </style>
</head>
<body>
 
 
<div class="container">
<h1 class='text-center my-4'>Mi Baul de Ternurines</h1>
<div class = "row justify-content-center">
<div class="card-container">
 
<?php
if (!empty($ternurines)) {
    foreach ($ternurines as $row) {       
            echo '<div class="card">';    
                      echo '<img class="card-img-top" src="' . $row["imagen"] . '" alt="Imagen de ternurin">';
 
               echo '<div class="card-body">';
        echo '<p class="card-text">Descripcion: ' . ($row["descripcion"] ? $row["descripcion"]: ''). '</p>';
 
        echo '<h5 class="card-title"> ' . $row["nombre"] . '</h5>';
       // echo '<p class="card-text">Nombre: ' . ($row["nombre"] ? $row["nombre"] : '') . '</p>';
        echo '<p class="card-text">Apellido: ' . ($row["apellido"] ? $row["apellido"] : '') . '</p>';
        echo '<p class="card-text">Fecha de nacimiento: ' . ($row["fecha_nacimiento"] ? $row["fecha_nacimiento"] : '') . '</p>';
        echo '<p class="card-text">Género: ' . ($row["genero"] ? $row["genero"] : '') . '</p>';  
        echo '<p class="card-text">Estado de nacimiento: ' . ($row["estado_nacimiento"] ? $row["estado_nacimiento"] : '') . '</p>';
        echo '<a href="editar.php?id=' . $row["id"] . '" class="btn btn-secondary">Editar</a>';
        echo '<a href ="generar_curp.php?id=' . $row["id"].'" class = "btn btn-primary"> Generar CURP</a>';
        echo '<form action="eliminar.php" method="POST" onsubmit="return confirm(\'¿Estas seguro de que quieres eliminar este ternurin del baul?\');">';
        echo '<input type="hidden" name="id" value="' . $row["id"] . '">';
        echo '<button type="submit" class="btn btn-danger">Eliminar</button>';

        echo '</form>';
        echo '</div>';    
        echo '</div>';
    }
} else {
    echo "No tienes ningún ternurin en tu baul";
}
?>
</div>
</div>
 
 
<script >
 
 
</script>
</body>
</html>