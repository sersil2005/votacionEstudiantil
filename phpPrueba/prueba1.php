<?php 
session_start();
require ("../conexion/conexion.php");

$user_codigo = $_SESSION['user_codigo'];
$votoCodigo = $_SESSION['votoCodigo'];
echo "Este es el codigo del usuario: " . $user_codigo . " Y este es el codigo del partido politico " . $votoCodigo;
?>