<?php
$server = "localhost";
$username = "root";
$password = "";
$dbname = "votacion";

$conn = new mysqli($server, $username, $password, $dbname);
if($conn->connect_errno){
    echo "Fallo al conectar a MySQL: " . $conn->connect_error;
}
?>