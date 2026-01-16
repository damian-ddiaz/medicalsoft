<?php
require_once 'conexion_bd.php'; 
mysqli_select_db($conn, 'medicalsoft'); 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id = $_GET['id'] ?? null;
$datos = null;

// Si hay ID, cargar datos para editar
if ($id) {
    $id = mysqli_real_escape_string($conn, $id);
    $res = mysqli_query($conn, "SELECT * FROM sistema_alergias WHERE id = $id");
    $datos = mysqli_fetch_assoc($res);
}

// PROCESAMIENTO DE POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_post = $_POST['id'] ?? null;
    $categoria = mysqli_real_escape_string($conn, $_POST['categoria']);
    $sustancia = mysqli_real_escape_string($conn, $_POST['sustancia']);
    $nivel_criticidad = mysqli_real_escape_string($conn, $_POST['nivel_criticidad']);
    $reaccion_descripcion = mysqli_real_escape_string($conn, $_POST['reaccion_descripcion']);
    
    try {
        if (isset($_POST['accion_guardar'])) {
            if (empty($id_post)) {
                $sql = "INSERT INTO sistema_alergias (categoria, sustancia, nivel_criticidad, reaccion_descripcion) 
                        VALUES ('$categoria', '$sustancia', '$nivel_criticidad', '$reaccion_descripcion')";
            } else {
                $sql = "UPDATE sistema_alergias SET categoria='$categoria', sustancia='$sustancia', 
                        nivel_criticidad='$nivel_criticidad', reaccion_descripcion='$reaccion_descripcion' 
                        WHERE id=$id_post";
            }
            mysqli_query($conn, $sql);
        }

        if (isset($_POST['accion_eliminar']) && !empty($id_post)) {
            mysqli_query($conn, "DELETE FROM sistema_alergias WHERE id = $id_post");
        }
        
        header("Location: pacientes_lista.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $error = "Error en la operación de base de datos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Alergia - MedicalSoft</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .main-container { max-width: 600px; margin: 40px auto; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; font-size: 0.8rem; color: #666; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="container main-container">
    <div class="card p-4">
        <h4 class="fw-bold mb-4 text-center"><?= $datos ? 'Editar Registro' : 'Nueva Alergia' ?></h4>
        
        <form method="POST">
            <input type="hidden" name="id" value="<?= $datos['id'] ?? '' ?>">
            
            <div class="mb-3">
                <label class="form-label">Categoría</label>
                <select name="categoria" class="form-select" required>
                    <?php $cat = $datos['categoria'] ?? ''; ?>
                    <option value="Alimentaria" <?= $cat == 'Alimentaria' ? 'selected' : '' ?>>Alimentaria</option>
                    <option value="Medicamentosa" <?= $cat == 'Medicamentosa' ? 'selected' : '' ?>>Medicamentosa</option>
                    <option value="Ambiental" <?= $cat == 'Ambiental' ? 'selected' : '' ?>>Ambiental</option>
                    <option value="Otra" <?= $cat == 'Otra' ? 'selected' : '' ?>>Otra</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Sustancia / Agente</label>
                <input type="text" name="sustancia" class="form-control" value="<?= $datos['sustancia'] ?? '' ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nivel de Criticidad</label>
                <select name="nivel_criticidad" class="form-select">
                    <?php $niv = $datos['nivel_criticidad'] ?? 'Moderado'; ?>
                    <option value="Bajo" <?= $niv == 'Bajo' ? 'selected' : '' ?>>Bajo</option>
                    <option value="Moderado" <?= $niv == 'Moderado' ? 'selected' : '' ?>>Moderado</option>
                    <option value="Alto" <?= $niv == 'Alto' ? 'selected' : '' ?>>Alto</option>
                    <option value="Vital/Anafilaxis" <?= $niv == 'Vital/Anafilaxis' ? 'selected' : '' ?>>Vital / Anafilaxis</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción de la Reacción</label>
                <textarea name="reaccion_descripcion" class="form-control" rows="3"><?= $datos['reaccion_descripcion'] ?? '' ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="accion_guardar" class="btn btn-primary flex-grow-1">
                    <?= $datos ? 'Actualizar' : 'Guardar' ?>
                </button>
                <?php if ($datos): ?>
                    <button type="submit" name="accion_eliminar" class="btn btn-outline-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                <?php endif; ?>
                <a href="pacientes_lista.php" class="btn btn-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>