<?php
require_once 'conexion_bd.php';
mysqli_select_db($conn, 'medicalsoft');

$id = $_GET['id'];
$res = mysqli_query($conn, "SELECT * FROM pacientes_ficha WHERE id = $id");
$p = mysqli_fetch_assoc($res);

if (!$p) { die("Paciente no encontrado"); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Médica - <?php echo $p['nombres']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none; } }
        body { font-size: 12px; }
        .ficha-header { border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
    </style>
</head>
<body onload="window.print()">
    <div class="container mt-4">
        <div class="no-print alert alert-info">Presiona Ctrl+P si el cuadro de impresión no se abre automáticamente.</div>
        
        <div class="ficha-header text-center">
            <h1>FICHA MÉDICA DE CONTROL</h1>
            <p>MedicalSoft System - Registro de Paciente</p>
        </div>

        <div class="row">
            <div class="col-6"><strong>Nombre:</strong> <?php echo $p['nombres'] . " " . $p['apellidos']; ?></div>
            <div class="col-6"><strong>ID:</strong> <?php echo $p['no_identificacion']; ?></div>
            <div class="col-4"><strong>F. Nacimiento:</strong> <?php echo $p['fecha_nacimiento']; ?></div>
            <div class="col-4"><strong>Género:</strong> <?php echo $p['genero']; ?></div>
            <div class="col-4"><strong>Grupo Sanguíneo:</strong> <?php echo $p['tipo_sangre']; ?></div>
        </div>

        <hr>
        <h5>Información de Contacto</h5>
        <p><strong>Teléfono:</strong> <?php echo $p['telefono_principal']; ?> | <strong>Correo:</strong> <?php echo $p['correo_electronico']; ?></p>
        <p><strong>Dirección:</strong> <?php echo $p['direccion_residencia']; ?></p>

        <hr>
        <h5>Antecedentes Médicos</h5>
        <p><strong>Alergias:</strong> <?php echo $p['alergias']; ?></p>
        <p><strong>Personales:</strong> <?php echo $p['antecedentes_personales']; ?></p>
        <p><strong>Familiares:</strong> <?php echo $p['antecedentes_familiares']; ?></p>
    </div>
</body>
</html>