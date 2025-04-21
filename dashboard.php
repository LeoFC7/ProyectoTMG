<?php
session_start();

// Verificar si el usuario está logueado
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: /ProyectoTMG/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema TMG</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-message">
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION["username"]); ?></h2>
        </div>
        
        <div class="user-info">
            <p><strong>Tipo de Usuario:</strong> <?php echo htmlspecialchars($_SESSION["tipo_usuario"]); ?></p>
            <p><strong>ID de Usuario:</strong> <?php echo htmlspecialchars($_SESSION["id"]); ?></p>
        </div>

        <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
</body>
</html> 