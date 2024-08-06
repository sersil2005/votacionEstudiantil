<?php
session_start();
include '../conexion/conexion.php';

$user = $_POST['user'];
$pass = $_POST['pass'];

$query = $conn->prepare("SELECT * FROM usuario WHERE correo = ? AND clave = ?");
$query->bind_param("ss", $user, $pass);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 1) {
    //Obtenemos la fila de los resultados
    $row = $result->fetch_assoc();
    //Aquí se guarda el codigo en la variable $_SESSION de la base de datos traida por $row.
    $_SESSION['user_codigo'] = $row['codigo'];
    if ($row['clave'] == '12345') {
        header("location: ../comprobacion/cambiarContraseña.html");
    } else {
        //Direccion del lugar de votacion.
        header("location: ../registreSuVoto/votar.php");
        exit();
    }
} else {
    echo "<script>alert('Correo o Usuario equivocados')</script>";
    echo "<script>window.location.href = '../Login.html'</script>";
}
?>