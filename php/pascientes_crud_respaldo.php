<?php
// 1. CONFIGURACIÓN Y CONEXIÓN
require_once 'conexion_bd.php'; 
mysqli_select_db($conn, 'medicalsoft'); 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mensaje = "";
$tipo_alerta = "alert-info";

// --- 2. PROCESAMIENTO DE DATOS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Limpieza de datos
    $id = (!empty($_POST['id'])) ? mysqli_real_escape_string($conn, $_POST['id']) : null;
    $nombres = mysqli_real_escape_string($conn, $_POST['nombres']);
    $apellidos = mysqli_real_escape_string($conn, $_POST['apellidos']);
    $identificacion = mysqli_real_escape_string($conn, $_POST['no_identificacion']);
    $fecha_nac = mysqli_real_escape_string($conn, $_POST['fecha_nacimiento']);
    $genero = mysqli_real_escape_string($conn, $_POST['genero']);
    $telefono = mysqli_real_escape_string($conn, $_POST['telefono_principal']);
    $correo = mysqli_real_escape_string($conn, $_POST['correo_electronico']);
    $direccion = mysqli_real_escape_string($conn, $_POST['direccion_residencia']);
    $contacto_emerg = mysqli_real_escape_string($conn, $_POST['contacto_emergencia']);
    $tel_emerg = mysqli_real_escape_string($conn, $_POST['tel_emergencia']);
    $sangre = mysqli_real_escape_string($conn, $_POST['tipo_sangre']);
    $alergias = mysqli_real_escape_string($conn, $_POST['alergias']);
    $ant_pers = mysqli_real_escape_string($conn, $_POST['antecedentes_personales']);
    $ant_fam = mysqli_real_escape_string($conn, $_POST['antecedentes_familiares']);
    
    try {
        if (isset($_POST['accion_guardar'])) {
            if (empty($id)) {
                // --- INSERTAR PACIENTE ---
                $sql = "INSERT INTO pacientes_ficha (nombres, apellidos, no_identificacion, fecha_nacimiento, genero, telefono_principal, correo_electronico, direccion_residencia, contacto_emergencia, tel_emergencia, tipo_sangre, alergias, antecedentes_personales, antecedentes_familiares) 
                        VALUES ('$nombres', '$apellidos', '$identificacion', '$fecha_nac', '$genero', '$telefono', '$correo', '$direccion', '$contacto_emerg', '$tel_emerg', '$sangre', '$alergias', '$ant_pers', '$ant_fam')";
                
                mysqli_query($conn, $sql);
                header("Location: " . $_SERVER['PHP_SELF'] . "?res=success");
                exit();
            } else {
                // --- ACTUALIZAR PACIENTE ---
                $sql = "UPDATE pacientes_ficha SET 
                        nombres='$nombres', apellidos='$apellidos', no_identificacion='$identificacion', 
                        fecha_nacimiento='$fecha_nac', genero='$genero', telefono_principal='$telefono', 
                        correo_electronico='$correo', direccion_residencia='$direccion', 
                        contacto_emergencia='$contacto_emerg', tel_emergencia='$tel_emerg', 
                        tipo_sangre='$sangre', alergias='$alergias', 
                        antecedentes_personales='$ant_pers', antecedentes_familiares='$ant_fam' 
                        WHERE id=$id";
                
                mysqli_query($conn, $sql);
                header("Location: " . $_SERVER['PHP_SELF'] . "?res=updated");
                exit();
            }
        }

        if (isset($_POST['accion_eliminar']) && !empty($id)) {
            $sql = "DELETE FROM pacientes_ficha WHERE id = $id";
            mysqli_query($conn, $sql);
            header("Location: " . $_SERVER['PHP_SELF'] . "?res=deleted");
            exit();
        }

    } catch (mysqli_sql_exception $e) {
        $err = ($e->getCode() == 1062) ? "duplicate" : "error";
        header("Location: " . $_SERVER['PHP_SELF'] . "?res=$err");
        exit();
    }
}

// --- 3. MENSAJES ---
if (isset($_GET['res'])) {
    switch ($_GET['res']) {
        case 'success': $mensaje = "✅ Paciente registrado."; $tipo_alerta = "alert-success"; break;
        case 'updated': $mensaje = "✅ Ficha actualizada."; $tipo_alerta = "alert-success"; break;
        case 'deleted': $mensaje = "🗑️ Paciente eliminado."; $tipo_alerta = "alert-warning"; break;
        case 'duplicate': $mensaje = "❌ Error: Cédula o Correo ya existen."; $tipo_alerta = "alert-danger"; break;
        case 'error': $mensaje = "❌ Error en la base de datos."; $tipo_alerta = "alert-danger"; break;
    }
}

$resultado = mysqli_query($conn, "SELECT * FROM pacientes_ficha ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Pacientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .main-container { max-width: 900px; margin: 40px auto; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; font-size: 0.8rem; color: #666; text-transform: uppercase; }
        .form-control { border-radius: 8px; }
    </style>
</head>
<body>

<div class="container main-container">
    <div class="card p-4 mb-4">
        <h2 class="text-center fw-bold mb-4">Ficha Médica del Paciente</h2>
        
        <?php if($mensaje): ?>
            <div class="alert <?= $tipo_alerta ?> py-2 text-center small"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST" id="formPaciente">
            <input type="hidden" name="id" id="p_id">
            
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Nombres</label>
                    <input type="text" name="nombres" id="p_nombres" class="form-control mb-3" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos" id="p_apellidos" class="form-control mb-3" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">No. Identificación</label>
                    <input type="text" name="no_identificacion" id="p_ident" class="form-control mb-3" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="p_fecha" class="form-control mb-3" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Género</label>
                    <select name="genero" id="p_genero" class="form-control mb-3">
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono_principal" id="p_tel" class="form-control mb-3">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Grupo Sanguíneo</label>
                    <select name="tipo_sangre" id="p_sangre" class="form-select mb-3">
                        <option value="">Seleccione...</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="correo_electronico" id="p_correo" class="form-control mb-3">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion_residencia" id="p_dir" class="form-control mb-3">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contacto Emergencia</label>
                    <input type="text" name="contacto_emergencia" id="p_cont_em" class="form-control mb-3">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tel. Emergencia</label>
                    <input type="text" name="tel_emergencia" id="p_tel_em" class="form-control mb-3">
                </div>

                <div class="col-12">
                    <label class="form-label">Alergias</label>
                    <textarea name="alergias" id="p_alergias" class="form-control mb-3" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Antecedentes Personales</label>
                    <textarea name="antecedentes_personales" id="p_ant_p" class="form-control mb-3" rows="3"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Antecedentes Familiares</label>
                    <textarea name="antecedentes_familiares" id="p_ant_f" class="form-control mb-3" rows="3"></textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" name="accion_guardar" id="btn_guardar" class="btn btn-primary flex-grow-1">Guardar Paciente</button>
              
                <button type="button" id="btn_imprimir" class="btn btn-dark" onclick="imprimirPDF()" disabled>
                    <i class="bi bi-printer"></i> Imprimir PDF
                </button>

                <button type="submit" name="accion_eliminar" id="btn_eliminar" class="btn btn-outline-danger" onclick="return confirm('¿Borrar paciente?')" disabled>Eliminar</button>
                <button type="button" onclick="resetForm()" class="btn btn-light">Limpiar</button>
            </div>
        </form>
    </div>

    <div class="card p-3 shadow-sm">
        <h6 class="fw-bold mb-3">Pacientes Recientes</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle small">
                <thead>
                    <tr>
                        <th>Identificación</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($p = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['no_identificacion']) ?></strong></td>
                        <td><?= htmlspecialchars($p['nombres'] . " " . $p['apellidos']) ?></td>
                        <td><?= htmlspecialchars($p['correo_electronico']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick='editar(<?= json_encode($p) ?>)'>Ver/Editar</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function editar(data) {
    document.getElementById('p_id').value = data.id;
    document.getElementById('p_nombres').value = data.nombres;
    document.getElementById('p_apellidos').value = data.apellidos;
    document.getElementById('p_ident').value = data.no_identificacion;
    document.getElementById('p_fecha').value = data.fecha_nacimiento;
    document.getElementById('p_genero').value = data.genero;
    document.getElementById('p_tel').value = data.telefono_principal;
    document.getElementById('p_correo').value = data.correo_electronico;
    document.getElementById('p_dir').value = data.direccion_residencia;
    document.getElementById('p_cont_em').value = data.contacto_emergencia;
    document.getElementById('p_tel_em').value = data.tel_emergencia;
    document.getElementById('p_sangre').value = data.tipo_sangre;
    document.getElementById('p_alergias').value = data.alergias;
    document.getElementById('p_ant_p').value = data.antecedentes_personales;
    document.getElementById('p_ant_f').value = data.antecedentes_familiares;

    document.getElementById('btn_eliminar').disabled = false;
    document.getElementById('btn_imprimir').disabled = false; // Habilitar imprimir
    document.getElementById('btn_guardar').innerText = 'Actualizar Paciente';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('formPaciente').reset();
    document.getElementById('p_id').value = '';
    document.getElementById('btn_eliminar').disabled = true;
    document.getElementById('btn_imprimir').disabled = true; // Deshabilitar imprimir
    document.getElementById('btn_guardar').innerText = 'Guardar Paciente';
}
function imprimirPDF() {
    const id = document.getElementById('p_id').value;
    if (id) {
        window.open('pasciente_ficha_pdf.php?id=' + id, '_blank');
    }
}
</script>

</body>
</html>