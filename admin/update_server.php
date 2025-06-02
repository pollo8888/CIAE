<?php
// update_server.php
header('Content-Type: application/json');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ciae";

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No se recibieron datos']);
    exit;
}

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit;
}

// Preparar la consulta UPDATE
$sql = "UPDATE servidores SET 
        ip = ?, 
        nombre = ?, 
        nombre_sv = ?, 
        nombre_db = ?, 
        usuario = ?, 
        contrasena = ?, 
        puerto = ? 
        WHERE id = ?";

$stmt = $conn->prepare($sql);

// Si la contraseña es "********", no la actualizamos
if ($data['contrasena'] === '********') {
    $sql = "UPDATE servidores SET 
            ip = ?, 
            nombre = ?, 
            nombre_sv = ?, 
            nombre_db = ?, 
            usuario = ?, 
            puerto = ? 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssii", 
        $data['ip'], 
        $data['nombre'], 
        $data['nombre_sv'], 
        $data['nombre_db'], 
        $data['usuario'], 
        $data['puerto'], 
        $data['id']
    );
} else {
    $stmt->bind_param("ssssssii", 
        $data['ip'], 
        $data['nombre'], 
        $data['nombre_sv'], 
        $data['nombre_db'], 
        $data['usuario'], 
        $data['contrasena'], 
        $data['puerto'], 
        $data['id']
    );
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Servidor actualizado correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
