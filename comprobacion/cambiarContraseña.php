<?php
include("../conexion/conexion.php");

//Declaracion de variables
$user = $_POST['user'];
$pass = $_POST['pass'];
$passTwo = $_POST['passTwo'];

//Nombre de la tabla
$table = "usuario";

try {
    //verificacion si el usuario existe en la base de datos
    $query = $conn->prepare("SELECT * FROM $table WHERE correo = ?");
    $query->bind_param('s', $user);
    $query->execute();
    $result = $query->get_result();

    //Si se encontro un usuario procede a hacer la condicional
    if($result->num_rows == 1){
        
    //Condicional si las contraseñas son iguales
    if ($pass == $passTwo) {
        //Si las contraseñas son iguales hace la declaracion update
        $queryUpdate =$conn->prepare("UPDATE $table SET clave = ? WHERE correo = ?");
        $queryUpdate->bind_param('ss', $pass, $user);

        //Aquí se verifica si se ejecuta por medio de esta condicional
        if ($queryUpdate->execute()) {
            header("location: ../Login.html");
        } else {
            echo "Error al actualizar la contraseña" . mysqli_error($conn);
        }
        //cerrando la sentencia de actualización
        $queryUpdate->close();

        //el else es por si las contraseñas no coinsiden
    } else {
        echo "Las contraseñas no coinciden";
    }
} else {
    echo "El usuario no existe";
}
//cerrando la sentencia de la seleccion
$query->close(); 
} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    //cerrando la conexion
    $conn->close();
}
?>