<?php
// 1. CONFIGURACIÓN Y CONEXIÓN
require_once 'conexion_bd.php'; 
mysqli_select_db($conn, 'medicalsoft'); 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mensaje = "";
$tipo_alerta = "alert-info";

// --- 2. PROCESAMIENTO DE DATOS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (!empty($_POST['id'])) ? mysqli_real_escape_string($conn, $_POST['id']) : null;
    $categoria = mysqli_real_escape_string($conn, $_POST['categoria']);
    $sustancia = mysqli_real_escape_string($conn, $_POST['sustancia']);
    $nivel_criticidad = mysqli_real_escape_string($conn, $_POST['nivel_criticidad']);
    $reaccion_descripcion = mysqli_real_escape_string($conn, $_POST['reaccion_descripcion']);
    
    try {
        if (isset($_POST['accion_guardar'])) {
            if (empty($id)) {
                // INSERTAR
                $sql = "INSERT INTO sistema_alergias (categoria, sustancia, nivel_criticidad, reaccion_descripcion) 
                        VALUES ('$categoria', '$sustancia', '$nivel_criticidad', '$reaccion_descripcion')";
                mysqli_query($conn, $sql);
                header("Location: " . $_SERVER['PHP_SELF'] . "?res=success");
                exit();
            } else {
                // ACTUALIZAR
                $sql = "UPDATE sistema_alergias SET 
                        categoria='$categoria', sustancia='$sustancia', 
                        nivel_criticidad='$nivel_criticidad', reaccion_descripcion='$reaccion_descripcion' 
                        WHERE id=$id";
                mysqli_query($conn, $sql);
                header("Location: " . $_SERVER['PHP_SELF'] . "?res=updated");
                exit();
            }
        }

        if (isset($_POST['accion_eliminar']) && !empty($id)) {
            $sql = "DELETE FROM sistema_alergias WHERE id = $id";
            mysqli_query($conn, $sql);
            header("Location: " . $_SERVER['PHP_SELF'] . "?res=deleted");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?res=error");
        exit();
    }
}

// --- 3. MENSAJES ---
if (isset($_GET['res'])) {
    switch ($_GET['res']) {
        case 'success': $mensaje = "✅ Alergia registrada correctamente."; $tipo_alerta = "alert-success"; break;
        case 'updated': $mensaje = "✅ Registro actualizado."; $tipo_alerta = "alert-success"; break;
        case 'deleted': $mensaje = "🗑️ Registro eliminado."; $tipo_alerta = "alert-warning"; break;
        case 'error': $mensaje = "❌ Error en la operación."; $tipo_alerta = "alert-danger"; break;
    }
}

$resultado = mysqli_query($conn, "SELECT * FROM sistema_alergias ORDER BY categoria ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Alergias - MedicalSoft</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .main-container { max-width: 800px; margin: 40px auto; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; font-size: 0.8rem; color: #666; text-transform: uppercase; }
        .badge-criticidad { font-size: 0.75rem; padding: 5px 10px; border-radius: 20px; }
    </style>
</head>
<body>

<div class="container main-container">
    <div class="card p-4 mb-4">
        <h3 class="text-center fw-bold mb-4">Catálogo de Alergias</h3>
        
        <?php if($mensaje): ?>
            <div class="alert <?= $tipo_alerta ?> py-2 text-center small"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST" id="formAlergia">
            <input type="hidden" name="id" id="a_id">
            
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" id="a_categoria" class="form-select mb-3" required>
                        <option value="Alimentaria">Alimentaria</option>
                        <option value="Medicamentosa">Medicamentosa</option>
                        <option value="Ambiental">Ambiental</option>
                        <option value="Otra">Otra</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sustancia / Agente</label>
                    <input type="text" name="sustancia" id="a_sustancia" class="form-control mb-3" placeholder="Ej: Penicilina, Maní..." required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Nivel de Criticidad</label>
                    <select name="nivel_criticidad" id="a_nivel" class="form-select mb-3">
                        <option value="Bajo">Bajo</option>
                        <option value="Moderado" selected>Moderado</option>
                        <option value="Alto">Alto</option>
                        <option value="Vital/Anafilaxis">Vital / Anafilaxis</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción de la Reacción</label>
                    <textarea name="reaccion_descripcion" id="a_descripcion" class="form-control mb-3" rows="3" placeholder="Describa los síntomas comunes..."></textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" name="accion_guardar" id="btn_guardar" class="btn btn-primary flex-grow-1">Guardar Registro</button>
                <button type="submit" name="accion_eliminar" id="btn_eliminar" class="btn btn-outline-danger" onclick="return confirm('¿Eliminar esta alergia del sistema?')" disabled>Eliminar</button>
                <button type="button" onclick="resetForm()" class="btn btn-light">Limpiar</button>
            </div>
        </form>
    </div>

    <div class="card p-3 shadow-sm">
        <h6 class="fw-bold mb-3">Alergias Registradas</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle small">
                <thead class="table-light">
                    <tr>
                        <th>Sustancia</th>
                        <th>Categoría</th>
                        <th>Criticidad</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($resultado)): 
                        $color = match($row['nivel_criticidad']) {
                            'Bajo' => 'bg-info',
                            'Moderado' => 'bg-warning text-dark',
                            'Alto' => 'bg-danger',
                            'Vital/Anafilaxis' => 'bg-dark',
                            default => 'bg-secondary'
                        };
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['sustancia']) ?></strong></td>
                        <td><?= htmlspecialchars($row['categoria']) ?></td>
                        <td><span class="badge badge-criticidad <?= $color ?>"><?= $row['nivel_criticidad'] ?></span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" onclick='editar(<?= json_encode($row) ?>)'>Editar</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-link text-decoration-none">← Volver a Ficha de Pacientes</a>
    </div>
</div>

<script>
function editar(data) {
    document.getElementById('a_id').value = data.id;
    document.getElementById('a_categoria').value = data.categoria;
    document.getElementById('a_sustancia').value = data.sustancia;
    document.getElementById('a_nivel').value = data.nivel_criticidad;
    document.getElementById('a_descripcion').value = data.reaccion_descripcion;

    document.getElementById('btn_eliminar').disabled = false;
    document.getElementById('btn_guardar').innerText = 'Actualizar Registro';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('formAlergia').reset();
    document.getElementById('a_id').value = '';
    document.getElementById('btn_eliminar').disabled = true;
    document.getElementById('btn_guardar').innerText = 'Guardar Registro';
}
</script>

</body>
</html>