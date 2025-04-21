<?php
require_once 'database.php';

// Datos del usuario y huella
$username = "usuario";  // Usuario al que queremos asociar la huella
$id_huella = 1;        // ID de la huella que registraste en el ESP32

// Actualizar el usuario con el ID de huella
$sql = "UPDATE usuarios SET id_huella = ? WHERE username = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "is", $id_huella, $username);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Huella asociada exitosamente al usuario: $username\n";
        echo "ID de huella: $id_huella\n";
    } else {
        echo "Error al asociar la huella: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?> 