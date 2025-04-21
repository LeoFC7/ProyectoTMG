<?php
session_start();

// Verificar si el usuario está logueado y es admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["tipo_usuario"] !== "admin"){
    header("Location: /ProyectoTMG/index.php");
    exit;
}

require_once "config/database.php";

$id = $_GET["id"] ?? 0;
$error_message = "";
$success_message = "";

// Obtener datos del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if(!$user) {
    header("Location: admin_panel.php");
    exit;
}

// Procesar formulario de edición
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST["nombre"]);
    $apellido_paterno = mysqli_real_escape_string($conn, $_POST["apellido_paterno"]);
    $apellido_materno = mysqli_real_escape_string($conn, $_POST["apellido_materno"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $direccion = mysqli_real_escape_string($conn, $_POST["direccion"]);
    $id_huella = !empty($_POST["id_huella"]) ? intval($_POST["id_huella"]) : null;
    
    // Si se proporcionó una nueva contraseña
    if(!empty($_POST["password"])) {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, email = ?, direccion = ?, password = ?, id_huella = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssii", $nombre, $apellido_paterno, $apellido_materno, $email, $direccion, $password, $id_huella, $id);
    } else {
        $sql = "UPDATE usuarios SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, email = ?, direccion = ?, id_huella = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssii", $nombre, $apellido_paterno, $apellido_materno, $email, $direccion, $id_huella, $id);
    }
    
    if(mysqli_stmt_execute($stmt)) {
        $success_message = "Usuario actualizado exitosamente";
        // Actualizar datos del usuario
        $user = array_merge($user, [
            'nombre' => $nombre,
            'apellido_paterno' => $apellido_paterno,
            'apellido_materno' => $apellido_materno,
            'email' => $email,
            'direccion' => $direccion,
            'id_huella' => $id_huella
        ]);
    } else {
        $error_message = "Error al actualizar usuario: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Usuario</h2>
        
        <?php if($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="<?php echo $user['nombre']; ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" class="form-control" value="<?php echo $user['apellido_paterno']; ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Apellido Materno</label>
                            <input type="text" name="apellido_materno" class="form-control" value="<?php echo $user['apellido_materno']; ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control" value="<?php echo $user['direccion']; ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>ID de Huella</label>
                        <input type="number" name="id_huella" class="form-control" value="<?php echo $user['id_huella'] ?? ''; ?>" min="1">
                        <small class="form-text text-muted">Dejar en blanco si no se tiene ID de huella</small>
                    </div>
                    <div class="form-group">
                        <label>Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="admin_panel.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 