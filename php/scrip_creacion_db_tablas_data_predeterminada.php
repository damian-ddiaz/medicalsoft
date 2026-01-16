<?php
// Script de reestructuración de base de datos
// Damian Diaz

// 1. IMPORTAR LA CONEXIÓN (Asegúrate de que conexion_bd.php ya NO pida el nombre de la BD al conectar)
require_once 'conexion_bd.php'; 

try {
    // --- PASO A: VALIDAR O CREAR LA BASE DE DATOS ---
    $target_db = 'medicalsoft';
    // --- PASO B: SELECCIONAR LA BASE DE DATOS PARA TRABAJAR ---
    $conn->select_db($target_db);
    echo "✅ CONEXIÓN AL SERVIDOR Y BD '$target_db' EXITOSA";
    echo '<br>';

    $var_decimal = "DECIMAL(15,2) DEFAULT 0.00";
    // Data Predeterminada 
    // --- sistema_alergias ---
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
    echo "\n ✅ Datos de especialidades medicas (A-Z) cargados correctamente. \n";

    // --- especialidades_medicas ---
    // --- Datos predeterminados (Ordenados A-Z) ---
    $especialidades_default = [
        ['Anestesiología', 'Cuidado y alivio del dolor antes y después de cirugías.'],
        ['Cardiología', 'Estudio y tratamiento de enfermedades del corazón.'],
        ['Dermatología', 'Tratamiento de afecciones de la piel, cabello y uñas.'],
        ['Endocrinología', 'Tratamiento de glándulas endocrinas y hormonas.'],
        ['Gastroenterología', 'Enfermedades del aparato digestivo.'],
        ['Ginecología y Obstetricia', 'Salud del sistema reproductor femenino y embarazo.'],
        ['Hematología', 'Tratamiento de enfermedades de la sangre.'],
        ['Medicina General', 'Atención primaria y diagnóstico preventivo.'],
        ['Medicina Interna', 'Atención integral del adulto en enfermedades complejas.'],
        ['Nefrología', 'Estudio de la estructura y función de los riñones.'],
        ['Neumología', 'Enfermedades del sistema respiratorio.'],
        ['Neurología', 'Tratamiento de trastornos del sistema nervioso.'],
        ['Oftalmología', 'Diagnóstico y tratamiento de enfermedades oculares.'],
        ['Oncología', 'Diagnóstico y tratamiento del cáncer.'],
        ['Otorrinolaringología', 'Enfermedades de oído, nariz y garganta.'],
        ['Pediatría', 'Atención médica de bebés, niños y adolescentes.'],
        ['Psiquiatría', 'Diagnóstico y tratamiento de trastornos mentales.'],
        ['Reumatología', 'Enfermedades de las articulaciones y tejidos conectivos.'],
        ['Traumatología y Ortopedia', 'Lesiones en el sistema músculo-esquelético.'],
        ['Urología', 'Afecciones del sistema urinario y aparato reproductor masculino.']
    ];

     // Ordenar alfabéticamente por el nombre (primer elemento del sub-array)
    sort($especialidades_default);

    foreach ($especialidades_default as $esp) {
        $nombre = $esp[0];
        $desc = $esp[1];

        $sql_insert = "INSERT INTO sistema_especialidades_medicas (`nombre`, `descripcion`, `activo`) 
                        VALUES ('$nombre', '$desc', 1)";
        $conn->query($sql_insert);
    }

    echo "\n ✅ Datos de especialidades medicas (A-Z) cargados correctamente. \n";

    } catch (mysqli_sql_exception $e) {
        die("❌ Error de ejecución SQL: " . $e->getMessage());
    }
?>