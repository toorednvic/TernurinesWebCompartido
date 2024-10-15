<?php
session_start();
class usuario {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function eliminar($id ) {

        try {
            echo $id ;
            $sql = "DELETE FROM usuarios WHERE id = ? ";
            $stmt = $this->db->connection->prepare($sql);
            if ($stmt === false) {
                throw new Exception('Error al preparar la consulta: ' . $this->db->connection->error);
            }      
              $stmt->bind_param("i", $id );
            $stmt->execute();
            var_dump($stmt);
            $stmt->store_result();
            

            if ($stmt->affected_rows > 0) {
                return "El usuario se elimino correctamente.";
            } else {
                throw new Exception("No se pudo eliminar el usuario. Revisa de que el ID del usuario es correcto.");
            }
            $stmt->close();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
include 'database.php';
$usuario = new usuario($db);
echo $usuario->eliminar($_POST['id']);
//echo $ternurin 
?>


