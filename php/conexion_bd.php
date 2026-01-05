<?php
// Tus credenciales de acceso
$host = '127.0.0.1';
$user = 'root';
$pass = 'Gemelas2000#';
$db   = 'medicalsoft'; // Define aquí el nombre de tu base de datos

// Activar el reporte de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 1. Conectar al servidor incluyendo el cuarto parámetro ($db)
    $conn = new mysqli($host, $user, $pass, $db);

    // 2. Establecer el juego de caracteres
    $conn->set_charset("utf8mb4");

    // Opcional: Mensaje de éxito (solo para pruebas)
    // echo "✅ Conexión a la base de datos exitosa.";

} catch (mysqli_sql_exception $e) {
    // Si el error es porque la base de datos no existe, te lo dirá claramente
    die("❌ Error de conexión: " . $e->getMessage());
}
?>