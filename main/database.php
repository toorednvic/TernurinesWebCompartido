<?php

class db {

    //propiedades para la conexion a la bd
    private $host = "localhost";
    private $username = "root";
    private $password = "12345";
    private $database = "TernurinDB";

    
    public $connection; 
 

    public function __construct() {

        try {
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
                  
            if ($this->connection->connect_error) {
                die("Error en la conexión: " . $this->connection->connect_error);
            }      
          } catch (Exception $e) {
           
            var_dump($e);
            
        }    }

    public function checkConnection() {

        if (mysqli_ping($this->connection)) {
        } else {
            echo "Alerta: Se perdio la conexion con el servidor.";
        }
    }
}
global $db;
$db = new db();

$db->checkConnection();


?>