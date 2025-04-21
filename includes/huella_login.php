<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que se recibió el ID de huella
    if (isset($_POST['id_huella'])) {
        $id_huella = mysqli_real_escape_string($conn, $_POST['id_huella']);
        
        $sql = "SELECT id, username, tipo_usuario FROM usuarios WHERE id_huella = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id_huella);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $username, $tipo_usuario);
                    if (mysqli_stmt_fetch($stmt)) {
                        session_start();
                        
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["tipo_usuario"] = $tipo_usuario;
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Login exitoso',
                            'redirect' => '/ProyectoTMG/dashboard.php'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Huella no registrada'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error en la consulta'
                ]);
            }
            
            mysqli_stmt_close($stmt);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ID de huella no proporcionado'
        ]);
    }
}

mysqli_close($conn);
?> 