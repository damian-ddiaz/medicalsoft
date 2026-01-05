<?php
require_once 'conexion_bd.php';

// Consultas rápidas para las métricas
try {
    $total_usuarios = $conn->query("SELECT COUNT(*) as total FROM sistema_usuarios")->fetch_assoc()['total'];
    $usuarios_activos = $conn->query("SELECT COUNT(*) as total FROM sistema_usuarios WHERE activo = 1")->fetch_assoc()['total'];
    $ultimos_usuarios = $conn->query("SELECT alias, nombre_completo FROM sistema_usuarios ORDER BY id DESC LIMIT 5");
} catch (Exception $e) {
    $total_usuarios = 0;
    $usuarios_activos = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedicalSoft - Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0061ff;
            --secondary-color: #6c757d;
            --bg-light: #f4f7f6;
            --sidebar-width: 250px;
        }

        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }

        /* Sidebar Styling */
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            min-height: 100vh;
            background: white;
            transition: all 0.3s;
            box-shadow: 2px 0 15px rgba(0,0,0,0.05);
            z-index: 1000;
        }

        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid #eee; }
        .sidebar-header h4 { color: var(--primary-color); font-weight: 800; margin: 0; }

        #sidebar .nav-link {
            padding: 15px 25px;
            color: #555;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.2s;
        }

        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            background-color: #f0f4ff;
            color: var(--primary-color);
            border-right: 4px solid var(--primary-color);
        }

        /* Main Content */
        #content { width: 100%; padding: 30px; }

        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .table-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            #sidebar { margin-left: calc(-1 * var(--sidebar-width)); position: absolute; }
            #sidebar.active { margin-left: 0; }
            #content { padding: 15px; }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4>MedicalSoft</h4>
            <small class="text-muted">Gestión Administrativa</small>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a href="#" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="usuarios_crud.php" class="nav-link"><i class="bi bi-people"></i> Usuarios</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"><i class="bi bi-person-badge"></i> Pacientes</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"><i class="bi bi-gear"></i> Configuración</a>
            </li>
            <li class="nav-item mt-5">
                <a href="login.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left"></i> Cerrar Sesión</a>
            </li>
        </ul>
    </nav>

    <div id="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button type="button" id="sidebarCollapse" class="btn btn-white shadow-sm d-md-none">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="fw-bold m-0">Bienvenido al Panel</h3>
            <div class="dropdown">
                <button class="btn btn-white shadow-sm dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> Admin
                </button>
                <ul class="dropdown-menu border-0 shadow">
                    <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="login.php">Salir</a></li>
                </ul>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">TOTAL USUARIOS</p>
                            <h2 class="fw-bold mb-0"><?= $total_usuarios ?></h2>
                        </div>
                        <div class="icon-box bg-primary text-white">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">ACTIVOS HOY</p>
                            <h2 class="fw-bold mb-0"><?= $usuarios_activos ?></h2>
                        </div>
                        <div class="icon-box bg-success text-white">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">CONEXIÓN BD</p>
                            <h2 class="fw-bold mb-0 text-primary">OK</h2>
                        </div>
                        <div class="icon-box bg-info text-white">
                            <i class="bi bi-hdd-network"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card table-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0">Nuevos Registros</h5>
                <a href="usuarios_crud.php" class="btn btn-sm btn-light text-primary fw-bold">Ver todos</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle border-light">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th>USUARIO</th>
                            <th>NOMBRE COMPLETO</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $ultimos_usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark fw-bold">@<?= htmlspecialchars($user['alias']) ?></span></td>
                            <td><?= htmlspecialchars($user['nombre_completo']) ?></td>
                            <td><span class="badge rounded-pill bg-success-subtle text-success">Activo</span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarCollapse').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>