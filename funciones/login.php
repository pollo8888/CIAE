<?php
session_start();
require_once '../db/conexion.php';

// Obtener datos POST
$tipo = $_POST['tipo']; // 'admin' o 'empleado'
$user = $_POST['usuario'];
$pass = $_POST['contrasena'];

// Consulta según tipo de usuario
if ($tipo === 'admin') {
    $query = "SELECT * FROM usuarios WHERE usuario = ? AND contrasena = ?";
} else {
    $query = "SELECT * FROM usuarios WHERE matricula = ? AND contrasena = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $user, $pass);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $datos = $resultado->fetch_assoc();

    // Guardar datos en variables de sesión (excepto la contraseña)
    $_SESSION['id'] = $datos['id'];
    $_SESSION['matricula'] = $datos['matricula'];
    $_SESSION['categoria'] = $datos['categoria'];
    $_SESSION['usuario'] = $datos['usuario'];
    $_SESSION['email'] = $datos['email'];
    $_SESSION['nombre'] = $datos['nombre'];
    $_SESSION['apellidos'] = $datos['apellidos'];
    $_SESSION['rol'] = $datos['rol'];
    $_SESSION['clinica'] = $datos['clinica'];
    $_SESSION['turno'] = $datos['turno'];
    $_SESSION['fecha_registro'] = $datos['fecha_registro'];

    echo json_encode([
        "success" => true,
        "rol" => $datos['rol']
    ]);
} else {
    echo json_encode(["success" => false, "mensaje" => "Credenciales incorrectas"]);
}

$stmt->close();
$conn->close();
?>
