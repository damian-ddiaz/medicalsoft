<?php
// Tus credenciales de acceso
$host = '127.0.0.1';
$user = 'root';
$pass = 'Gemelas2000#';
// $db   = 'medicalsoft'; // 1. MANTÉN ESTO COMENTADO o elímina el cuarto parámetro abajo

// Activar el reporte de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 2. CORRECCIÓN VITAL: Conectamos solo al servidor (sin el cuarto parámetro $db)
    $conn = new mysqli($host, $user, $pass); 

    // 3. Establecer el juego de caracteres
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    die("❌ Error de conexión al servidor: " . $e->getMessage());
}
?>