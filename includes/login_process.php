<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT id, username, password, tipo_usuario FROM usuarios WHERE username = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $tipo_usuario);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["tipo_usuario"] = $tipo_usuario;
                        
                        header("Location: /ProyectoTMG/dashboard.php");
                        exit;
                    } else {
                        $login_err = "Usuario o contraseña incorrectos";
                    }
                }
            } else {
                $login_err = "Usuario o contraseña incorrectos";
            }
        } else {
            echo "Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
        }
        
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?> 