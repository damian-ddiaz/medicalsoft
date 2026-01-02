<?php
session_start();
require_once 'conexion_bd.php';

$mensaje = "";

// 1. Proceso de Autenticación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alias_o_correo = mysqli_real_escape_string($conn, $_POST['usuario']);
    $password = $_POST['password'];

    // Buscamos por alias o por correo electrónico
    $sql = "SELECT id, alias, contrasena, activo FROM sistema_usuarios WHERE (alias = ? OR correo_electronico = ?) LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $alias_o_correo, $alias_o_correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($usuario = $resultado->fetch_assoc()) {
        // Verificamos si la cuenta está activa
        if ($usuario['activo'] == 0) {
            $mensaje = '<div class="alert alert-warning">Esta cuenta está desactivada.</div>';
        } else {
            // Verificación de contraseña (asumiendo que usas password_hash en el registro)
            if (password_verify($password, $usuario['contrasena'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['alias'] = $usuario['alias'];
                
                // Redirigir al sistema
                header("Location: dashboard.php");
                exit();
            } else {
                $mensaje = '<div class="alert alert-danger">Contraseña incorrecta.</div>';
            }
        }
    } else {
        $mensaje = '<div class="alert alert-danger">El usuario no existe.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 15px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="card">
        <div class="card-body p-5">
            <h3 class="text-center mb-4">Iniciar Sesión</h3>
            
            <?php echo $mensaje; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario o Correo</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Escriba su alias o email" required>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                </div>
            </form>
            
            <div class="mt-3 text-center">
                <small class="text-muted">Sistema de Gestión de Pacientes</small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>