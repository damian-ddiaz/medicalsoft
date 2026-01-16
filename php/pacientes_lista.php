<?php
require_once 'conexion_bd.php'; 
mysqli_select_db($conn, 'medicalsoft'); 

// Obtener registros
$resultado = mysqli_query($conn, "SELECT * FROM sistema_alergias ORDER BY categoria ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Alergias - MedicalSoft</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .main-container { max-width: 900px; margin: 40px auto; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .badge-criticidad { font-size: 0.75rem; padding: 5px 10px; border-radius: 20px; }
    </style>
</head>
<body>

<div class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0">Catálogo de Alergias</h3>
        <a href="pacientes_ficha_crud.php" class="btn btn-primary">+ Nueva Alergia</a>
    </div>

    <div class="card p-3 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Sustancia</th>
                        <th>Categoría</th>
                        <th>Criticidad</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($resultado)): 
                        switch ($row['nivel_criticidad']) {
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
                                $color = 'bg-dark';
                                break;
                            default:
                                $color = 'bg-secondary';
                                break;
                        }
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['sustancia']) ?></strong></td>
                        <td><?= htmlspecialchars($row['categoria']) ?></td>
                        <td><span class="badge badge-criticidad <?= $color ?>"><?= $row['nivel_criticidad'] ?></span></td>
                        <td class="text-end">
                            <a href="pacientes_ficha_crud.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>