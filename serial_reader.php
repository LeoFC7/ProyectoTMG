<?php
// Iniciar sesión al principio del script
session_start();

require_once 'config/database.php';

// Configuración del puerto serial
$port = 'COM5'; // Ajusta según tu puerto COM
$baud = 115200;

// Abrir puerto serial
$fp = fopen($port, 'r+');
if (!$fp) {
    die("Error: No se pudo abrir el puerto serial");
}

// Configurar el puerto
exec("mode $port BAUD=$baud PARITY=N data=8 stop=1 xon=off");

echo "Esperando datos del ESP32...\n";

while (true) {
    if ($line = fgets($fp)) {
        $line = trim($line);
        
        // Verificar si es un mensaje de huella
        if (strpos($line, 'HUELLA:') === 0) {
            $id_huella = intval(substr($line, 7));
            
            // Buscar usuario con ese ID de huella
            $sql = "SELECT id, username, tipo_usuario FROM usuarios WHERE id_huella = ?";
            
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $id_huella);
                
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    
                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        mysqli_stmt_bind_result($stmt, $id, $username, $tipo_usuario);
                        if (mysqli_stmt_fetch($stmt)) {
                            // Actualizar variables de sesión
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["tipo_usuario"] = $tipo_usuario;
                            
                            // Actualizar archivo de estado
                            $status = [
                                "status" => "active",
                                "username" => $username,
                                "timestamp" => date("Y-m-d H:i:s")
                            ];
                            file_put_contents("login_status.json", json_encode($status));
                            
                            // Enviar respuesta al ESP32
                            fwrite($fp, "OK\n");
                            echo "Login exitoso para usuario: $username\n";
                        }
                    } else {
                        fwrite($fp, "ERROR\n");
                        echo "Huella no registrada\n";
                        
                        // Actualizar archivo de estado
                        $status = [
                            "status" => "error",
                            "username" => "",
                            "timestamp" => date("Y-m-d H:i:s")
                        ];
                        file_put_contents("login_status.json", json_encode($status));
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
    usleep(100000); // Pequeña pausa para no sobrecargar la CPU
}

fclose($fp);
?> 