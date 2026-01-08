<?php
// 1. CONFIGURACIÓN Y CONEXIÓN
require_once 'conexion_bd.php'; 
mysqli_select_db($conn, 'medicalsoft'); // Agrega esta línea

// Habilitar reporte de errores para capturar excepciones en el bloque try-catch
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mensaje = "";
$tipo_alerta = "alert-info";

// --- 2. PROCESAMIENTO DE DATOS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Limpiamos los datos básicos
    $id = (!empty($_POST['id'])) ? mysqli_real_escape_string($conn, $_POST['id']) : null;
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre_completo']);
    $correo = mysqli_real_escape_string($conn, $_POST['correo_electronico']);
    $alias = mysqli_real_escape_string($conn, $_POST['alias']);
    
    try {
        if (isset($_POST['accion_guardar'])) {
            if (empty($id)) {
                // --- INSERTAR NUEVO USUARIO ---
                $pass_raw = $_POST['contrasena'];
                if(empty($pass_raw)) {
                    throw new Exception("La contraseña es obligatoria para nuevos registros.");
                }
                
                $pass = password_hash($pass_raw, PASSWORD_BCRYPT);
                $ip = $_SERVER['REMOTE_ADDR'];

                $sql = "INSERT INTO sistema_usuarios (alias, correo_electronico, contrasena, nombre_completo, ip_estacion, activo) 
                        VALUES ('$alias', '$correo', '$pass', '$nombre', '$ip', 1)";
                
                mysqli_query($conn, $sql);
                header("Location: " . $_SERVER['PHP_SELF'] . "?res=success");
                exit();

            } else {
                // --- ACTUALIZAR USUARIO EXISTENTE ---
                // Nota: En actualización no tocamos la contraseña según tu lógica original
                $sql = "UPDATE sistema_usuarios SET alias='$alias', correo_electronico='$correo', nombre_completo='$nombre' WHERE id=$id";
                
                mysqli_query($conn, $sql);
                header("Location: " . $_SERVER['PHP_SELF'] . "?res=updated");
                exit();
            }
        }

        if (isset($_POST['accion_eliminar']) && !empty($id)) {
            // --- ELIMINAR USUARIO ---
            $sql = "DELETE FROM sistema_usuarios WHERE id = $id";
            mysqli_query($conn, $sql);
            header("Location: " . $_SERVER['PHP_SELF'] . "?res=deleted");
            exit();
        }

    } catch (mysqli_sql_exception $e) {
        // Error 1062 es para entradas duplicadas (Unique keys en BD)
        $err = ($e->getCode() == 1062) ? "duplicate" : "error";
        header("Location: " . $_SERVER['PHP_SELF'] . "?res=$err");
        exit();
    } catch (Exception $e) {
        $mensaje = "⚠️ " . $e->getMessage();
        $tipo_alerta = "alert-warning";
    }
}

// --- 3. CAPTURA DE MENSAJES TRAS REDIRECCIÓN (PRG Pattern) ---
if (isset($_GET['res'])) {
    switch ($_GET['res']) {
        case 'success': $mensaje = "✅ Usuario guardado con éxito."; $tipo_alerta = "alert-success"; break;
        case 'updated': $mensaje = "✅ Datos actualizados correctamente."; $tipo_alerta = "alert-success"; break;
        case 'deleted': $mensaje = "🗑️ Usuario eliminado del sistema."; $tipo_alerta = "alert-warning"; break;
        case 'duplicate': $mensaje = "❌ Error: El Alias o Correo ya existen."; $tipo_alerta = "alert-danger"; break;
        case 'error': $mensaje = "❌ Error crítico en la base de datos."; $tipo_alerta = "alert-danger"; break;
    }
}

// --- 4. OBTENER LISTA PARA LA TABLA ---
$resultado = mysqli_query($conn, "SELECT * FROM sistema_usuarios ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Registro Único</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .main-container { max-width: 500px; margin: 40px auto; }
        .card { border-radius: 20px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .btn-custom { padding: 12px; border-radius: 12px; font-weight: 600; transition: 0.3s; flex: 1; border: none; }
        .btn-guardar { background-color: #0061ff; color: white; }
        .btn-guardar:hover { background-color: #0052d6; }
        .btn-eliminar { background-color: #fff; color: #dc3545; border: 1px solid #dc3545; }
        .btn-eliminar:hover:not(:disabled) { background-color: #dc3545; color: white; }
        .btn-salir { background-color: #6c757d; color: white; text-decoration: none; text-align: center; }
        .form-control { border-radius: 10px; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; }
        .form-label { font-weight: 600; font-size: 0.85rem; color: #555; margin-bottom: 5px; display: block; }
        .table-section { margin-top: 30px; background: white; border-radius: 15px; padding: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="main-container">
        
        <div class="card p-4 p-md-5">
            <h2 class="text-center fw-bold mb-4">Usuarios del Sistema</h2>
            
            <?php if($mensaje): ?>
                <div class="alert <?= $tipo_alerta ?> py-2 text-center small shadow-sm"><?= $mensaje ?></div>
            <?php endif; ?>

            <form method="POST" id="formUsuario">
                <input type="hidden" name="id" id="user_id">
                
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre_completo" id="nombre" class="form-control" placeholder="Nombre y Apellidos">

                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="correo_electronico" id="correo" class="form-control" placeholder="correo@ejemplo.com" required>

                <label class="form-label">Alias</label>
                <input type="text" name="alias" id="alias" class="form-control" placeholder="Usuario único" required>

                <div id="section_pass">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="Mínimo 8 caracteres">
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="submit" name="accion_guardar" id="btn_principal" class="btn-custom btn-guardar shadow-sm">Guardar</button>
                    <button type="submit" name="accion_eliminar" id="btn_eliminar_form" class="btn-custom btn-eliminar" 
                            onclick="return confirm('¿Seguro que desea eliminar este registro?')" disabled>Eliminar</button>
                    <a href="index.php" class="btn-custom btn-salir d-flex align-items-center justify-content-center">Salir</a>
                </div>
                
                <div class="text-center mt-4">
                    <a href="javascript:void(0)" onclick="resetForm()" class="text-decoration-none small text-secondary">Limpiar / Nuevo Registro</a>
                </div>
            </form>
        </div>

        <div class="table-section shadow-sm">
            <h6 class="fw-bold mb-3 text-secondary">Usuarios Registrados</h6>
            <table class="table table-hover align-middle mb-0 small">
                <tbody>
                    <?php if(mysqli_num_rows($resultado) > 0): ?>
                        <?php while($u = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($u['alias']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($u['correo_electronico']) ?></div>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary py-0" 
                                        onclick="editar('<?= $u['id'] ?>', '<?= addslashes($u['alias']) ?>', '<?= addslashes($u['correo_electronico']) ?>', '<?= addslashes($u['nombre_completo']) ?>')">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2" class="text-center text-muted">No hay usuarios registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function editar(id, alias, correo, nombre) {
        document.getElementById('user_id').value = id;
        document.getElementById('alias').value = alias;
        document.getElementById('correo').value = correo;
        document.getElementById('nombre').value = nombre;
        
        // Ocultar campo contraseña al editar (opcional, según tu lógica)
        document.getElementById('section_pass').style.display = 'none';
        document.getElementById('btn_eliminar_form').disabled = false;
        document.getElementById('btn_principal').innerText = 'Guardar Cambios';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('formUsuario').reset();
        document.getElementById('user_id').value = '';
        document.getElementById('section_pass').style.display = 'block';
        document.getElementById('btn_eliminar_form').disabled = true;
        document.getElementById('btn_principal').innerText = 'Guardar';
    }
</script>

</body>
</html>