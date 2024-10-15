<?php
session_start();
class Ternurin {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function eliminar($id, $id_usuario) {

        try {
            $sql = "DELETE FROM Ternurines_Personalizados WHERE id = ? AND id_usuario = ?";
            $stmt = $this->db->connection->prepare($sql);
            if ($stmt === false) {
                throw new Exception('Error al preparar la consulta: ' . $this->db->connection->error);
            }        $stmt->bind_param("ii", $id, $id_usuario);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->affected_rows > 0) {
                
                echo'<script type="text/javascript">
                alert("El ternurin se elimino correctamente");
                window.history.go(-1);
                        </script>';
            } else {
                  echo'<script type="text/javascript">
                alert("No se pudo eliminar el ternurin. Revisa de que el ID del ternurin es correcto y pertenece al usuario actual.");
                window.history.go(-1);
                        </script>';
                        throw new Exception("No se pudo eliminar el ternurin. Revisa de que el ID del ternurin es correcto y pertenece al usuario actual.");
                
              
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
include 'database.php';
$ternurin = new Ternurin($db);
echo $ternurin->eliminar($_POST['id'], $_SESSION['id_usuario']);
//echo $ternurin 
?>


