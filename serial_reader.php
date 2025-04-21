<?php
// Iniciar sesión al principio del script
session_start();

require_once 'config/database.php';

// Configuración del puerto serial
$port = 'COM5'; // Ajusta según tu puerto COM
$baud = 115200;

echo "Intentando conectar con el puerto $port...\n";

// Abrir puerto serial
$fp = @fopen($port, 'r+');
if (!$fp) {
    echo "Error: No se pudo abrir el puerto serial $port\n";
    echo "Posibles causas:\n";
    echo "1. El puerto no existe\n";
    echo "2. El puerto está siendo usado por otro programa\n";
    echo "3. No tienes permisos para acceder al puerto\n";
    echo "\nSugerencias:\n";
    echo "1. Verifica que el ESP32 esté conectado\n";
    echo "2. Revisa el Administrador de dispositivos para ver el puerto correcto\n";
    echo "3. Cierra otros programas que puedan estar usando el puerto\n";
    die();
}

// Configurar el puerto
echo "Configurando puerto serial...\n";
exec("mode $port BAUD=$baud PARITY=N data=8 stop=1 xon=off");

echo "Esperando datos del ESP32...\n";
echo "Si no recibes datos, verifica:\n";
echo "1. Que el ESP32 esté encendido\n";
echo "2. Que esté correctamente conectado\n";
echo "3. Que el código del ESP32 esté corriendo\n";

while (true) {
    if ($line = fgets($fp)) {
        $line = trim($line);
        echo "Datos recibidos: $line\n";
        
        // Verificar si es un mensaje de huella
        if (strpos($line, 'HUELLA:') === 0) {
            $id_huella = intval(substr($line, 7));
            echo "ID de huella detectado: $id_huella\n";
            
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