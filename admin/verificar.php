<?php
// verificar.php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ciae";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar la base de datos para obtener los servidores
$servidores = [];
$sql = "SELECT * FROM servidores";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Guardar los resultados en el array $servidores
    while ($row = $result->fetch_assoc()) {
        $servidores[] = [
            "id" => $row["id"],
            "ip" => $row["ip"],
            "ubicacion" => $row["ubicacion"],
            "nombre" => $row["nombre"],
            "nombre_sv" => $row["nombre_sv"],
            "nombre_db" => $row["nombre_db"],
            "usuario" => $row["usuario"],
            "contrasena" => $row["contrasena"],
            "puerto" => $row["puerto"]
        ];
    }
}

// Cerrar la conexión
$conn->close();

// Ruta del ejecutable de Python y el script
$python = "C:\\Users\\Soporte\\AppData\\Local\\Programs\\Python\\Python39\\python.exe";
$script = "C:\\xampp\\htdocs\\CIAE\\admin\\verificar_sql.py";

$resultados = [];

// Ejecutar el script para cada servidor
foreach ($servidores as $s) {
    $ip = $s["ip"];
    $ubicacion = $s["ubicacion"];
    
    // Si tienes las credenciales, puedes pasarlas al script de Python
    // Por ahora solo usamos el IP
    $comando = "\"$python\" \"$script\" $ip 2>&1";
    
    // Si tu script Python acepta más parámetros:
    // $usuario = $s["usuario"];
    // $contrasena = $s["contrasena"];
    // $puerto = $s["puerto"];
    // $comando = "\"$python\" \"$script\" $ip $usuario $contrasena $puerto 2>&1";
    
    $salida = shell_exec($comando);
    if (!$salida) {
        $resultados[] = array_merge($s, [
            "server" => $ip,
            "status" => "error",
            "error" => "Sin salida"
        ]);
        continue;
    }
    $json = json_decode($salida, true);
    if ($json) {
        $resultados[] = array_merge($s, $json);
    } else {
        $resultados[] = array_merge($s, [
            "server" => $ip,
            "status" => "error",
            "error" => "JSON inválido: $salida"
        ]);
    }
}

// Devolver los resultados como JSON
header("Content-Type: application/json");
echo json_encode($resultados, JSON_PRETTY_PRINT);
?>
