<?php
// Configurar headers para evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
require_once 'config/database.php';

// Verificar si ya está logueado
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: dashboard.php");
    exit;
}

// Leer el archivo de estado
$status_file = "login_status.json";
if(file_exists($status_file)) {
    $status = json_decode(file_get_contents($status_file), true);
    
    // Verificar si el estado es activo y no ha expirado (5 minutos)
    if($status["status"] === "active") {
        $login_time = strtotime($status["timestamp"]);
        $current_time = time();
        $time_diff = $current_time - $login_time;
        
        // Si el login es reciente (menos de 5 minutos)
        if($time_diff < 300) { // 300 segundos = 5 minutos
            // Obtener información del usuario
            $sql = "SELECT id, username, tipo_usuario FROM usuarios WHERE username = ?";
            
            if($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $status["username"]);
                
                if(mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    
                    if(mysqli_stmt_num_rows($stmt) == 1) {
                        mysqli_stmt_bind_result($stmt, $id, $username, $tipo_usuario);
                        if(mysqli_stmt_fetch($stmt)) {
                            // Actualizar variables de sesión
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["tipo_usuario"] = $tipo_usuario;
                            
                            // Redirigir al dashboard
                            header("location: dashboard.php");
                            exit;
                        }
                    }
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            // Si el login ha expirado, actualizar el estado
            $status = [
                "status" => "inactive",
                "username" => "",
                "timestamp" => date("Y-m-d H:i:s")
            ];
            file_put_contents($status_file, json_encode($status));
        }
    }
}

// Si no está logueado o el login ha expirado, mostrar la página de login
header("location: login.php");
exit;
?> 