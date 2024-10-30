<?php
include 'database.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
  
if (isset($_POST['id_usuario']) && isset($_POST['id_ternurin'])) {
    $id_usuario = $_POST['id_usuario'];
    $id_ternurin = $_POST['id_ternurin'];
 
    //echo "id_usuario: ". $id_usuario;
    //echo " id_ternurin: ". $id_ternurin;

        $sql = "INSERT INTO Ternurines_Personalizados (id_usuario, id_ternurin) VALUES (?, ?)";
        $stmt = $db->connection->prepare($sql);
    $stmt->bind_param("ii", $id_usuario, $id_ternurin);
    $stmt->execute();
 
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        header('Location: home.php');
        exit;
    } else {

        if(!isset($_POST['id_usuario'])){
            echo "no se recibio el id usuario";
        }
        $stmt->close();
        echo "Falló al agregar el ternurin al baul";
        
    }
} else {
    echo "No se recibieron los datos necesarios";
}
?>