<?php
require_once __DIR__ . '/database.php';

$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$tipo_usuario = "admin";

// Verificar si el usuario admin ya existe
$check_sql = "SELECT id FROM usuarios WHERE username = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $username);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if(mysqli_stmt_num_rows($check_stmt) == 0) {
    // Insertar usuario admin
    $sql = "INSERT INTO usuarios (username, password, tipo_usuario) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $username, $password, $tipo_usuario);
    
    if(mysqli_stmt_execute($stmt)) {
        echo "Usuario administrador creado exitosamente.<br>";
        echo "Usuario: admin<br>";
        echo "Contraseña: admin123";
    } else {
        echo "Error al crear el usuario administrador: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "El usuario administrador ya existe.";
}

mysqli_stmt_close($check_stmt);
mysqli_close($conn);
?> 