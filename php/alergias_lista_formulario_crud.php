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
                $sql = "INSERT INTO sistema_alergias (categoria, sustancia, nivel_criticidad, reaccion_descripcion) 
                        VALUES ('$categoria', '$sustancia', '$nivel_criticidad', '$reaccion_descripcion')";
                mysqli_query($conn, $sql);
                header("Location: " . $_SERVER['PHP_SELF'] . "?res=success");
                exit();
            } else {
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
        case 'success': $mensaje = "✅ Registrada."; $tipo_alerta = "alert-success"; break;
        case 'updated': $mensaje = "✅ Actualizada."; $tipo_alerta = "alert-success"; break;
        case 'deleted': $mensaje = "🗑️ Eliminada."; $tipo_alerta = "alert-warning"; break;
        case 'error': $mensaje = "❌ Error en BD."; $tipo_alerta = "alert-danger"; break;
    }
}

$resultado = mysqli_query($conn, "SELECT * FROM sistema_alergias ORDER BY categoria ASC, sustancia ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Alergias - MedicalSoft</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; font-size: 0.75rem; color: #666; text-transform: uppercase; }
        .badge-criticidad { font-size: 0.7rem; padding: 4px 8px; border-radius: 12px; }
        .sticky-form { position: sticky; top: 20px; }
        .table-container { background: white; border-radius: 15px; padding: 20px; }
    </style>
</head>
<body>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0"><i class="bi bi-list-check"></i> Alergias Registradas</h4>
                    <a href="index.php" class="btn btn-sm btn-light text-primary fw-bold">Volver a Fichas</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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
                                $nivel = $row['nivel_criticidad'];
                                switch ($nivel) {
                                    case 'Bajo':
                                        $color = 'bg-info';
                                        break;
                                    case 'Moderado':
                                        $color = 'bg-warning text-dark';
                                        break;
                                    case 'Alto':
                                        $color = 'bg-danger';
                                        break;
                                    case 'Vital/Anafilaxis':
                                        $color = 'bg-dark text-white';
                                        break;
                                    default:
                                        $color = 'bg-secondary';
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['sustancia']) ?></div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                        <?= htmlspecialchars($row['reaccion_descripcion']) ?>
                                    </small>
                                </td>
                                <td><span class="text-muted small"><?= $row['categoria'] ?></span></td>
                                <td><span class="badge badge-criticidad <?= $color ?>"><?= $row['nivel_criticidad'] ?></span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" onclick='editar(<?= json_encode($row) ?>)'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-form">
                <div class="card p-4">
                    <h5 class="fw-bold mb-3" id="form-title">Nueva Alergia</h5>
                    
                    <?php if($mensaje): ?>
                        <div class="alert <?= $tipo_alerta ?> py-2 text-center small mb-3"><?= $mensaje ?></div>
                    <?php endif; ?>

                    <form method="POST" id="formAlergia">
                        <input type="hidden" name="id" id="a_id">
                        
                        <label class="form-label">Categoría</label>
                        <select name="categoria" id="a_categoria" class="form-select mb-3" required>
                            <option value="Alimentaria">Alimentaria</option>
                            <option value="Medicamentosa">Medicamentosa</option>
                            <option value="Ambiental">Ambiental</option>
                            <option value="Otra">Otra</option>
                        </select>

                        <label class="form-label">Sustancia</label>
                        <input type="text" name="sustancia" id="a_sustancia" class="form-control mb-3" required>

                        <label class="form-label">Nivel de Criticidad</label>
                        <select name="nivel_criticidad" id="a_nivel" class="form-select mb-3">
                            <option value="Bajo">Bajo</option>
                            <option value="Moderado" selected>Moderado</option>
                            <option value="Alto">Alto</option>
                            <option value="Vital/Anafilaxis">Vital / Anafilaxis</option>
                        </select>

                        <label class="form-label">Descripción de Reacción</label>
                        <textarea name="reaccion_descripcion" id="a_descripcion" class="form-control mb-3" rows="3"></textarea>

                        <div class="d-grid gap-2 mt-2">
                            <button type="submit" name="accion_guardar" id="btn_guardar" class="btn btn-primary">
                                Guardar Registro
                            </button>
                            <div class="d-flex gap-2">
                                <button type="submit" name="accion_eliminar" id="btn_eliminar" class="btn btn-outline-danger flex-grow-1" onclick="return confirm('¿Eliminar?')" disabled>
                                    Eliminar
                                </button>
                                <button type="button" onclick="resetForm()" class="btn btn-light border flex-grow-1">
                                    Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editar(data) {
    document.getElementById('a_id').value = data.id;
    document.getElementById('a_categoria').value = data.categoria;
    document.getElementById('a_sustancia').value = data.sustancia;
    document.getElementById('a_nivel').value = data.nivel_criticidad;
    document.getElementById('a_descripcion').value = data.reaccion_descripcion;

    document.getElementById('form-title').innerText = 'Editar Alergia';
    document.getElementById('btn_eliminar').disabled = false;
    document.getElementById('btn_guardar').innerText = 'Actualizar Registro';
    document.getElementById('btn_guardar').className = 'btn btn-success';
}

function resetForm() {
    document.getElementById('formAlergia').reset();
    document.getElementById('a_id').value = '';
    document.getElementById('form-title').innerText = 'Nueva Alergia';
    document.getElementById('btn_eliminar').disabled = true;
    document.getElementById('btn_guardar').innerText = 'Guardar Registro';
    document.getElementById('btn_guardar').className = 'btn btn-primary';
}
</script>

</body>
</html>