<?php
// Tus credenciales de acceso
$host = '127.0.0.1';
$user = 'root';
$pass = 'Gemelas2000#';
$db   = 'medicalsoft'; // 1. QUITAMOS EL COMENTARIO PARA ACTIVAR LA VARIABLE

// Activar el reporte de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 2. CORRECCIÓN: Ahora incluimos $db como cuarto parámetro
    $conn = new mysqli($host, $user, $pass, $db);

    // 3. Establecer el juego de caracteres
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    // Si la base de datos 'medicalsoft' no existe, aquí te dará el detalle exacto
    die("❌ Error de conexión: " . $e->getMessage());
}
?>