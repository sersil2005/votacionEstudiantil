<?php
session_start();
require "../conexion/conexion.php";

$user_codigo = $_SESSION['user_codigo'];

if (isset($_SESSION['user_codigo'])) {
    $sql = $conn->prepare("
        SELECT a.nombres, a.apellidos 
        FROM alumno a
        INNER JOIN usuario u 
        ON a.codigoUsuarioFK = u.codigo 
        WHERE u.codigo = ?
    ");
    $sql->bind_param("i", $user_codigo);
    $sql->execute();
    $result = $sql->get_result();
} else {
    // Por si el usuario no existe
    header("location: ../Login.html");
}

// Inicializando variables
$nombres = "";
$apellidos = "";

if($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombres = $row['nombres'];
    $apellidos = $row['apellidos'];
}  else {
    // Maneja el caso donde no se encontraron datos del alumno
    echo "<script>alert('No se encontraron datos del alumno.');</script>";
    echo "<script>window.location.href = '../Login.html';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votación Sistem</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h1>Bienvenido al sistema de votación Estudiantil</h1>
    <form id="voteForm" method="post">
        <div class="row">
            <div class="col">
                <label for="">Nombre:</label>
                <input type="text" class="form-control" value="<?php echo $nombres; ?>" readonly>
            </div>
            <div class="col">
                <label for="">Apellido:</label>
                <input type="text" class="form-control" value="<?php echo $apellidos; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="">Grupo de votación</label>
                <select class="form-select" name="partidoPolitico">
                <?php
                $table = "partidopolitico";
                // Preparando la consulta
                $query = $conn->prepare("SELECT * FROM $table");
                // Ejecutando la consulta
                $query->execute();
                // Obteniendo los resultados
                $__getRersult = $query->get_result();
                ?>
                <?php foreach ($__getRersult as $__getRersult) : ?>
                    <option value="<?php echo $__getRersult['nombre']; ?>"><?php echo $__getRersult['nombre']; ?></option>
                <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-outline-success">¡Votar!</button>
        </div>
    </form>

    <div id="alertSuccess" class="alert alert-success mt-5" role="alert" style="display: none;">
        Su voto ha sido guardado. Puede regresar a la página principal aquí:
        <a class="btn btn-success" href="../Login.html">Página principal</a>
    </div>
    <div id="alertError" class="alert alert-danger mt-5" role="alert" style="display: none;">
        No se pudo registrar su voto. Puede regresar a la página principal aquí:
        <a class="btn btn-danger" href="../Login.html">Página principal</a>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#voteForm").submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: './registrarVoto.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $("#alertSuccess").show();
                    $("#alertError").hide();
                } else {
                    $("#alertSuccess").hide();
                    $("#alertError").show();
                }
            },
            error: function() {
                $("#alertSuccess").hide();
                $("#alertError").show().text('Error al procesar la solicitud.');
            }
        });
    });
});
</script>

</body>
</html>
