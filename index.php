<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: dashboard.php");
    exit;
}

require_once "config/database.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    require_once "includes/login_process.php";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; margin: 0 auto; }
        .huella-status {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .huella-status.waiting {
            background-color: #fff3cd;
            color: #856404;
        }
        .huella-status.success {
            background-color: #d4edda;
            color: #155724;
        }
        .huella-status.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Por favor ingresa tus credenciales o usa tu huella digital.</p>

        <div id="huellaStatus" class="huella-status waiting">
            Coloca tu dedo en el sensor de huella...
        </div>

        <form action="includes/login.php" method="post">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="username" class="form-control">
            </div>    
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>

    <script>
        function verificarHuella() {
            fetch('includes/verificar_huella.php')
                .then(response => response.json())
                .then(data => {
                    if(data.status === "success") {
                        document.getElementById('huellaStatus').className = 'huella-status success';
                        document.getElementById('huellaStatus').textContent = 'Login exitoso, redirigiendo...';
                        window.location.href = 'dashboard.php';
                    } else if(data.status === "inactive") {
                        document.getElementById('huellaStatus').className = 'huella-status waiting';
                        document.getElementById('huellaStatus').textContent = 'Coloca tu dedo en el sensor de huella...';
                    } else {
                        document.getElementById('huellaStatus').className = 'huella-status error';
                        document.getElementById('huellaStatus').textContent = 'Error: ' + data.message;
                    }
                })
                .catch(error => {
                    document.getElementById('huellaStatus').className = 'huella-status error';
                    document.getElementById('huellaStatus').textContent = 'Error al verificar huella';
                });
        }

        // Verificar cada segundo
        setInterval(verificarHuella, 1000);
    </script>
</body>
</html> 