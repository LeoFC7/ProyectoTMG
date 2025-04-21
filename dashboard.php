<?php
session_start();

// Verificar si el usuario está logueado
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: /ProyectoTMG/index.php");
    exit;
}

require_once "config/database.php";

// Obtener información completa del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema TMG</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .dashboard-container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        .welcome-message {
            text-align: center;
            margin-bottom: 20px;
        }
        .user-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none; /* Ocultar por defecto */
        }
        .btn-logout {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-logout:hover {
            background-color: #c82333;
        }
        .action-buttons {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-message">
            <h2>Bienvenido, <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido_paterno'] . ' ' . $user['apellido_materno']); ?></h2>
        </div>

        <div class="action-buttons">
            <button onclick="toggleUserInfo()" class="btn btn-primary">Ver mis datos</button>
            <?php if($user['tipo_usuario'] === 'admin'): ?>
                <a href="admin_panel.php" class="btn btn-success">Panel de Administración</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>

        <div id="userInfo" class="user-info">
            <h4>Información de tu cuenta</h4>
            <p><strong>Usuario:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'No especificado'); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($user['direccion'] ?? 'No especificada'); ?></p>
            <p><strong>Tipo de Usuario:</strong> <?php echo htmlspecialchars($user['tipo_usuario']); ?></p>
        </div>
    </div>

    <script>
        function toggleUserInfo() {
            const userInfo = document.getElementById('userInfo');
            if (userInfo.style.display === 'none' || userInfo.style.display === '') {
                userInfo.style.display = 'block';
            } else {
                userInfo.style.display = 'none';
            }
        }
    </script>
</body>
</html> 