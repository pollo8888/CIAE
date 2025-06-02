<?php
// verificar_individual.php
header('Content-Type: application/json');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ciae";

// Obtener el IP del servidor
$ip = $_GET['ip'] ?? null;

if (!$ip) {
    echo json_encode(['status' => 'error', 'error' => 'IP no proporcionada']);
    exit;
}

// Ruta del ejecutable de Python y el script
$python = "C:\\Users\\Soporte\\AppData\\Local\\Programs\\Python\\Python39\\python.exe";
$script = "C:\\xampp\\htdocs\\CIAE\\admin\\verificar_sql.py";

// Ejecutar el script de verificación
$comando = "\"$python\" \"$script\" $ip 2>&1";
$salida = shell_exec($comando);

if (!$salida) {
    echo json_encode(['status' => 'error', 'error' => 'Sin salida']);
    exit;
}

$resultado = json_decode($salida, true);
if ($resultado) {
    echo json_encode($resultado);
} else {
    echo json_encode(['status' => 'error', 'error' => 'JSON inválido: ' . $salida]);
}
?>
