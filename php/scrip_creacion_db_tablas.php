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

// --- sistema_alergias ---
    $nombre_tabla_alergias = 'sistema_alergias';
    $result_alergias = $conn->query("SHOW TABLES LIKE '$nombre_tabla_alergias'");

    if ($result_alergias->num_rows == 0) {
        echo "\n 🆕 Tabla '$nombre_tabla_alergias' no existe. Creando...\n";

        $create_alergias_sql = "
            CREATE TABLE `$nombre_tabla_alergias` (
                `id`                    INT(11) NOT NULL AUTO_INCREMENT,
                `categoria`             ENUM('Alimentaria', 'Medicamentosa', 'Ambiental', 'Otra') NOT NULL,
                `sustancia`             VARCHAR(100) NOT NULL,
                `nivel_criticidad`      ENUM('Bajo', 'Moderado', 'Alto', 'Vital/Anafilaxis') DEFAULT 'Moderado',
                `reaccion_descripcion`  TEXT,
                PRIMARY KEY (`id`),
                INDEX `idx_sustancia` (`sustancia`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        if ($conn->query($create_alergias_sql)) {
            echo " ✅ Tabla '$nombre_tabla_alergias' creada correctamente.\n";
        }

        // --- Generando Data predeterminda ---
        $alergias_iniciales = [
            ['Medicamentosa', 'Penicilina', 'Vital/Anafilaxis', 'Shock anafiláctico, dificultad respiratoria.'],
            ['Alimentaria', 'Maní (Cacahuate)', 'Vital/Anafilaxis', 'Hinchazón de garganta y cierre de vías aéreas.'],
            ['Medicamentosa', 'Aspirina', 'Alto', 'Asma inducida y urticaria grave.'],
            ['Ambiental', 'Látex', 'Moderado', 'Dermatitis de contacto e inflamación local.'],
            ['Alimentaria', 'Mariscos', 'Alto', 'Vómitos, urticaria y posible anafilaxia.'],
            ['Ambiental', 'Polen de Gramíneas', 'Bajo', 'Rinitis alérgica y lagrimeo.'],
            ['Medicamentosa', 'Sulfonamidas', 'Alto', 'Erupciones cutáneas severas (Síndrome de Stevens-Johnson).'],
            ['Alimentaria', 'Leche de vaca', 'Moderado', 'Trastornos digestivos y eccema.'],
            ['Otra', 'Veneno de Abeja', 'Vital/Anafilaxis', 'Reacción sistémica inmediata.'],
            ['Medicamentosa', 'Ibuprofeno', 'Moderado', 'Hinchazón facial y sibilancias.']
            ];

        foreach ($alergias_iniciales as $alergia) {
            $sql = "INSERT INTO sistema_alergias (categoria, sustancia, nivel_criticidad, reaccion_descripcion) 
                    VALUES ('{$alergia[0]}', '{$alergia[1]}', '{$alergia[2]}', '{$alergia[3]}')";
            $conn->query($sql);
        }




    } else {
        echo "\n 🛠 La tabla '$nombre_tabla_alergias' ya existe. Aplicando modificaciones...\n";

        $alter_alergias_sqls = [
            "MODIFY COLUMN `id`                    INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `categoria`             ENUM('Alimentaria', 'Medicamentosa', 'Ambiental', 'Otra') NOT NULL",
            "MODIFY COLUMN `sustancia`             VARCHAR(100) NOT NULL",
            "MODIFY COLUMN `nivel_criticidad`      ENUM('Bajo', 'Moderado', 'Alto', 'Vital/Anafilaxis') DEFAULT 'Moderado'",
            "MODIFY COLUMN `reaccion_descripcion`  TEXT"
        ];

        foreach ($alter_alergias_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla_alergias` $sql");
        }
        echo " ✅ Modificaciones en '$nombre_tabla_alergias' aplicadas.\n";
    }

    // --- sistema_usuarios ---
    $nombre_tabla = 'sistema_usuarios';
    $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");

    if ($result->num_rows == 0) {
        echo "🆕 Tabla '$nombre_tabla' no existe. Creando...";
        echo '<br>';

        $create_usuarios_sql = "
            CREATE TABLE `$nombre_tabla` (
                `id`                            INT(11) NOT NULL AUTO_INCREMENT,
                `alias`                         VARCHAR(50) NOT NULL,
                `correo_electronico`            VARCHAR(150) NOT NULL,
                `contrasena`                    VARCHAR(255) NOT NULL,
                `nombre_completo`               VARCHAR(100) DEFAULT '',
                `activo`                        TINYINT(1) DEFAULT 1,
                `token_recuperacion`            VARCHAR(255) DEFAULT '',
                `ip_estacion`                   VARCHAR(40) DEFAULT '',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_usuario` (`alias`),
                UNIQUE KEY `uk_email` (`correo_electronico`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $conn->query($create_usuarios_sql);
        echo "\n ✅ Tabla '$nombre_tabla' creada correctamente. \n";

    } else {
        echo "\n 🛠 La tabla '$nombre_tabla' ya existe. Aplicando modificaciones...\n";

        $alter_usuarios_sqls = [
            "MODIFY COLUMN `id`                 INT(11) NOT NULL AUTO_INCREMENT",
            "MODIFY COLUMN `alias`              VARCHAR(50) NOT NULL",
            "MODIFY COLUMN `correo_electronico` VARCHAR(150) NOT NULL",
            "MODIFY COLUMN `contrasena`         VARCHAR(255) NOT NULL",
            "MODIFY COLUMN `nombre_completo`    VARCHAR(100) DEFAULT ''",
            "MODIFY COLUMN `activo`             TINYINT(1) DEFAULT 1",
            "MODIFY COLUMN `token_recuperacion` VARCHAR(255) DEFAULT ''",
            "MODIFY COLUMN `ip_estacion`        VARCHAR(40) DEFAULT ''",
        ];

        foreach ($alter_usuarios_sqls as $sql) {
            $conn->query("ALTER TABLE `$nombre_tabla` $sql");
        }

        echo "\n ✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente. \n";
    }

    // --- pacientes_ficha ---
    $nombre_tabla = 'pacientes_ficha';
    $result = $conn->query("SHOW TABLES LIKE '$nombre_tabla'");

    if ($result->num_rows == 0) {
    echo "\n 🆕 Tabla '$nombre_tabla' no existe. Creando...\n";

    $create_pacientes_sql = "
        CREATE TABLE `$nombre_tabla` (
            `id`                                INT(11) NOT NULL AUTO_INCREMENT,
            `nombres`                           VARCHAR(100) NOT NULL,
            `apellidos`                         VARCHAR(100) NOT NULL,
            `no_identificacion`                 VARCHAR(20) NOT NULL,
            `fecha_nacimiento`                  DATE NOT NULL,
            `genero`                            VARCHAR(20) DEFAULT '',
            `telefono_principal`                VARCHAR(20) DEFAULT '',
            `correo_electronico`                VARCHAR(150) DEFAULT '',
            `direccion_residencia`              TEXT,
            `contacto_emergencia`               VARCHAR(150) DEFAULT '',
            `tel_emergencia`                    VARCHAR(20) DEFAULT '',
            `tipo_sangre`                       VARCHAR(5) DEFAULT '',
            `alergias`                          TEXT,
            `antecedentes_personales`           TEXT,
            `antecedentes_familiares`           TEXT,
            `fecha_registro`                    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_paciente_id` (`no_identificacion`),
            UNIQUE KEY `uk_paciente_email` (`correo_electronico`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";

    $conn->query($create_pacientes_sql);
    echo "\n ✅ Tabla '$nombre_tabla' creada correctamente.\n";

} else {
    echo "\n 🛠 La tabla '$nombre_tabla' ya existe. Aplicando modificaciones...\n";

    $alter_pacientes_sqls = [
        "MODIFY COLUMN `id`                     INT(11) NOT NULL AUTO_INCREMENT",
        "MODIFY COLUMN `nombres`                VARCHAR(100) NOT NULL",
        "MODIFY COLUMN `apellidos`              VARCHAR(100) NOT NULL",
        "MODIFY COLUMN `no_identificacion`      VARCHAR(20) NOT NULL",
        "MODIFY COLUMN `fecha_nacimiento`       DATE NOT NULL",
        "MODIFY COLUMN `genero`                 VARCHAR(20) DEFAULT ''",
        "MODIFY COLUMN `telefono_principal`     VARCHAR(20) DEFAULT ''",
        "MODIFY COLUMN `correo_electronico`     VARCHAR(150) DEFAULT ''",
        "MODIFY COLUMN `direccion_residencia`   TEXT",
        "MODIFY COLUMN `contacto_emergencia`    VARCHAR(150) DEFAULT ''",
        "MODIFY COLUMN `tel_emergencia`         VARCHAR(20) DEFAULT ''",
        "MODIFY COLUMN `tipo_sangre`            VARCHAR(5) DEFAULT ''",
        "MODIFY COLUMN `alergias`               TEXT",
        "MODIFY COLUMN `antecedentes_personales`TEXT",
        "MODIFY COLUMN `antecedentes_familiares`TEXT"
    ];

    foreach ($alter_pacientes_sqls as $sql) {
        $conn->query("ALTER TABLE `$nombre_tabla` $sql");
    }

    echo "\n ✅ Estructura de la tabla '$nombre_tabla' actualizada exitosamente.\n";
}


    echo "\n ✅ ✅ ESTRUCTURA BD PROCESADA CORRECTAMENTE ✅ ✅...";
    $conn->close();

} catch (mysqli_sql_exception $e) {
    die("❌ Error de ejecución SQL: " . $e->getMessage());
}
?>