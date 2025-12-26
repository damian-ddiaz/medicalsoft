<?php
// Script de reestructuración de base de datos
// Damian Diaz

// 1. IMPORTAR LA CONEXIÓN (Asegúrate de que conexion_bd.php ya NO pida el nombre de la BD al conectar)
require_once 'conexion_bd.php'; 

try {
    // --- PASO A: VALIDAR O CREAR LA BASE DE DATOS ---
    $target_db = 'medicalsoft';

    // Verificamos si la base de datos existe
    $db_check = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$target_db'");

    if ($db_check->num_rows == 0) {
        echo "🆕 La base de datos '$target_db' no existe. Creándola...";
        echo '<br>';
        $conn->query("CREATE DATABASE `$target_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "✅ Base de datos '$target_db' creada correctamente.";
        echo '<br>';
    }

    // --- PASO B: SELECCIONAR LA BASE DE DATOS PARA TRABAJAR ---
    $conn->select_db($target_db);
    echo "✅ CONEXIÓN AL SERVIDOR Y BD '$target_db' EXITOSA";
    echo '<br>';

    $var_decimal = "DECIMAL(15,2) DEFAULT 0.00";

    // --- GESTIÓN DE USUARIOS ---
    $nombre_tabla = 'sistema_usuarios';
    $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");

    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '<br>';

        $create_usuarios_sql = "
            CREATE TABLE `$nombre_tabla` (
                `id`                  INT(11) NOT NULL AUTO_INCREMENT,
                `alias`               VARCHAR(50) NOT NULL,
                `correo_electronico`  VARCHAR(150) NOT NULL,
                `contrasena`          VARCHAR(255) NOT NULL,
                `nombre_completo`     VARCHAR(100) DEFAULT '',
                `activo`              TINYINT(1) DEFAULT 1,
                `token_recuperacion`  VARCHAR(255) DEFAULT '',
                `ip_estacion`         VARCHAR(40) DEFAULT '',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_usuario` (`alias`),
                UNIQUE KEY `uk_email` (`correo_electronico`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $conn->query($create_usuarios_sql);
        echo "✅ Tabla '$nombre_tabla' creada correctamente.";
        echo '<br>';

    } else {
        echo "🛠 La tabla '$nombre_tabla' ya existe. Aplicando modificaciones...";
        echo '<br>';

        $alter_usuarios_sqls = [
            "MODIFY COLUMN `id`                  INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `alias`               VARCHAR(50) NOT NULL",
            "MODIFY COLUMN `correo_electronico`  VARCHAR(150) NOT NULL",
            "MODIFY COLUMN `contrasena`          VARCHAR(255) NOT NULL",
            "MODIFY COLUMN `nombre_completo`     VARCHAR(100) DEFAULT ''",
            "MODIFY COLUMN `activo`              TINYINT(1) DEFAULT 1",
            "MODIFY COLUMN `token_recuperacion`  VARCHAR(255) DEFAULT ''",
            "MODIFY COLUMN `ip_estacion`         VARCHAR(40) DEFAULT ''",
        ];

        foreach ($alter_usuarios_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla` $sql");
        }

        echo "✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente.";
        echo '<br>';  
    }

    echo "✅ ✅ ESTRUCTURA BD PROCESADA CORRECTAMENTE ✅ ✅...";
    $conn->close();

} catch (mysqli_sql_exception $e) {
    die("❌ Error de ejecución SQL: " . $e->getMessage());
}
?>