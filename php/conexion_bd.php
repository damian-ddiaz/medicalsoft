<?php
// Tus credenciales de acceso
$host = '127.0.0.1';
$user = 'root';
$pass = 'Gemelas2000#';

// Activar el reporte de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Intentar realizar la conexión sin pasar el parámetro de $target_db
    $conn = new mysqli($host, $user, $pass);

    // Establecer el juego de caracteres
    $conn->set_charset("utf8mb4");

    // Opcional: Mensaje de éxito
  //   echo "✅ Conexión al servidor exitosa.";

} catch (mysqli_sql_exception $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}

// Nota: Para usar una base de datos más adelante, deberás usar:
// $conn->select_db('medicalsoft');
?>