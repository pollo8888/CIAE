<?php
session_start();
require_once '../db/conexion.php';

// Obtener datos POST
$tipo = $_POST['tipo']; // 'admin' o 'empleado'
$user = $_POST['usuario'];
$pass = $_POST['contrasena'];

if ($tipo === 'admin') {
    $query = "SELECT * FROM usuarios WHERE usuario = ? AND contrasena = ?";
} else {
    $query = "SELECT * FROM usuarios WHERE matricula = ? AND contrasena = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $user, $pass);
$stmt->execute();
$resultado = $stmt->get_result();

// Dentro del if de éxito
if ($resultado->num_rows > 0) {
    $datos = $resultado->fetch_assoc();
    $_SESSION['usuario'] = $datos['usuario'];
    $_SESSION['rol'] = $datos['rol'];
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
