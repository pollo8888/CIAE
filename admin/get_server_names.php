<?php
// get_server_names.php
header('Content-Type: application/json');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ciae";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(['error' => 'Conexión fallida']));
}

// Consultar nombres de servidores
$serverNames = [];
$sql = "SELECT ubicacion, nombre FROM servidores";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $serverNames[$row["ubicacion"]] = $row["nombre"];
    }
}

$conn->close();

echo json_encode($serverNames);
?>
