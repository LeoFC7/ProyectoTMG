<?php
session_start();

// Verificar si el usuario está logueado y es admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["tipo_usuario"] !== "admin"){
    header("Location: /ProyectoTMG/index.php");
    exit;
}

require_once "config/database.php";

// Procesar formulario de nuevo usuario
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    if($_POST["action"] == "create") {
        $username = mysqli_real_escape_string($conn, $_POST["username"]);
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $tipo_usuario = "normal";
        $nombre = mysqli_real_escape_string($conn, $_POST["nombre"]);
        $apellido_paterno = mysqli_real_escape_string($conn, $_POST["apellido_paterno"]);
        $apellido_materno = mysqli_real_escape_string($conn, $_POST["apellido_materno"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $direccion = mysqli_real_escape_string($conn, $_POST["direccion"]);
        $id_huella = !empty($_POST["id_huella"]) ? intval($_POST["id_huella"]) : null;
        
        $sql = "INSERT INTO usuarios (username, password, tipo_usuario, nombre, apellido_paterno, apellido_materno, email, direccion, id_huella) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssssi", $username, $password, $tipo_usuario, $nombre, $apellido_paterno, $apellido_materno, $email, $direccion, $id_huella);
            
            if(mysqli_stmt_execute($stmt)) {
                $success_message = "Usuario creado exitosamente";
            } else {
                $error_message = "Error al crear usuario: " . mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Obtener lista de usuarios
$sql = "SELECT id, username, nombre, apellido_paterno, apellido_materno, email, tipo_usuario, direccion, id_huella FROM usuarios";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container { margin-top: 20px; }
        .table { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Panel de Administración</h2>
        
        <?php if(isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h4>Nuevo Usuario</h4>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <input type="hidden" name="action" value="create">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Usuario</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Apellido Materno</label>
                            <input type="text" name="apellido_materno" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>ID de Huella (opcional)</label>
                        <input type="number" name="id_huella" class="form-control" min="1">
                        <small class="form-text text-muted">Dejar en blanco si no se tiene ID de huella</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </form>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Dirección</th>
                        <th>ID Huella</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['apellido_paterno'] . ' ' . $row['apellido_materno']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['tipo_usuario']; ?></td>
                        <td><?php echo $row['direccion']; ?></td>
                        <td><?php echo $row['id_huella'] ?? 'No asignado'; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
    </div>
</body>
</html> 