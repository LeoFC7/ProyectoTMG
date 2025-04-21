<?php
session_start();
require_once '../config/database.php';

// Leer el archivo de estado
$status_file = "../login_status.json";
$response = ["status" => "inactive", "message" => ""];

if(file_exists($status_file)) {
    $status = json_decode(file_get_contents($status_file), true);
    
    if($status["status"] === "active") {
        $login_time = strtotime($status["timestamp"]);
        $current_time = time();
        $time_diff = $current_time - $login_time;
        
        if($time_diff < 300) { // 5 minutos
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
                            
                            $response["status"] = "success";
                            $response["message"] = "Login exitoso";
                            
                            // Resetear el estado
                            $status = [
                                "status" => "inactive",
                                "username" => "",
                                "timestamp" => date("Y-m-d H:i:s")
                            ];
                            file_put_contents($status_file, json_encode($status));
                        }
                    }
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            // Login expirado
            $status = [
                "status" => "inactive",
                "username" => "",
                "timestamp" => date("Y-m-d H:i:s")
            ];
            file_put_contents($status_file, json_encode($status));
        }
    }
} else {
    $response["message"] = "Archivo de estado no encontrado";
}

header('Content-Type: application/json');
echo json_encode($response);
?> 