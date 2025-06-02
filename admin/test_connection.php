<?php
// test_connection.php
header('Content-Type: application/json');

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No se recibieron datos']);
    exit;
}

// Intentar ejecutar el script de Python con los parámetros recibidos
$python = "C:\\Users\\Soporte\\AppData\\Local\\Programs\\Python\\Python39\\python.exe";
$script = "C:\\xampp\\htdocs\\CIAE\\admin\\verificar_sql.py";

// Si tienes un script específico para probar conexión con credenciales, úsalo
// Por ahora, intentamos con el IP
$ip = $data['ip'];
$comando = "\"$python\" \"$script\" $ip 2>&1";
$salida = shell_exec($comando);

if (!$salida) {
    echo json_encode([
        'success' => false,
        'error' => 'No se pudo ejecutar la prueba de conexión'
    ]);
    exit;
}

$resultado = json_decode($salida, true);

if ($resultado && isset($resultado['status'])) {
    if ($resultado['status'] === 'online') {
        echo json_encode([
            'success' => true,
            'message' => 'Conexión exitosa',
            'tables' => $resultado['tables'] ?? 0
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $resultado['error'] ?? 'No se pudo conectar al servidor'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Respuesta inválida del servidor'
    ]);
}
?>
