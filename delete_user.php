<?php
session_start();

// Verificar si el usuario está logueado y es admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["tipo_usuario"] !== "admin"){
    header("Location: /ProyectoTMG/index.php");
    exit;
}

require_once "config/database.php";

$id = $_GET["id"] ?? 0;

if($id) {
    // No permitir eliminar al usuario admin
    $sql = "SELECT username FROM usuarios WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $username);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        if($username === "admin") {
            $_SESSION["error_message"] = "No se puede eliminar al usuario administrador";
            header("Location: admin_panel.php");
            exit;
        }
    }
    
    // Eliminar el usuario
    $sql = "DELETE FROM usuarios WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if(mysqli_stmt_execute($stmt)) {
            $_SESSION["success_message"] = "Usuario eliminado exitosamente";
        } else {
            $_SESSION["error_message"] = "Error al eliminar usuario: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

header("Location: admin_panel.php");
exit;
?> 