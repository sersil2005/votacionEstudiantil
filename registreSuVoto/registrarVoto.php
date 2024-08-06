<?php 
session_start();
include '../conexion/conexion.php';

$response = array('status' => '', 'message' => '');

// Condicional si se presionó el botón
if (isset($_POST['partidoPolitico'])) {
    // Variables del formulario
    $partidoPolitico = $_POST['partidoPolitico'];
    
    // Identificación de la tabla
    $table = 'partidopolitico';

    // Preparación de la consulta
    $sql = $conn->prepare("SELECT * FROM $table WHERE nombre = ?");
    $sql->bind_param('s', $partidoPolitico);
    $sql->execute();
    $result = $sql->get_result();
    $partido = $result->fetch_assoc();

    // Verificar si se encontró un partido
    if ($partido) {
        $_SESSION['votoCodigo'] = $partido['codigo']; 
        $user_codigo = $_SESSION['user_codigo'];
        
        // Tabla de la base de datos voto
        $tableVoto = "voto";

        // Verificar si el usuario ya ha votado
        $checkVote = $conn->prepare("SELECT * FROM $tableVoto WHERE codigoAlumnoFK = ?");
        $checkVote->bind_param('i', $user_codigo);
        $checkVote->execute();
        $voteResult = $checkVote->get_result();

        if ($voteResult->num_rows > 0) {
            $response['status'] = 'error';
            $response['message'] = 'Ya has votado anteriormente.';
        } else {
            // Preparación de la consulta de inserción
            $insertVoto = $conn->prepare("INSERT INTO $tableVoto (codigoAlumnoFK, codigoPartidoPoliticoFK) VALUES (?, ?)");
            // Dos parámetros, el código del alumno y el código del partido político
            $insertVoto->bind_param('ii', $user_codigo, $partido['codigo']);
            // Ejecuta la consulta
            if ($insertVoto->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Voto registrado exitosamente.';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Error al registrar el voto: ' . $insertVoto->error;
            }
            // Cerrar la consulta de inserción
            $insertVoto->close();
        }
        
        // Cerrar la consulta de verificación de voto
        $checkVote->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'No se encontró un partido.';
    }

    // Cerrar la consulta de selección de partido
    $sql->close();
} else {
    $response['status'] = 'error';
    $response['message'] = 'No se ha enviado ningún partido político.';
}

echo json_encode($response);
?>

