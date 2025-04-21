<?php
require_once 'database.php';

// SQL para modificar la tabla usuarios
$sql = "ALTER TABLE usuarios
ADD COLUMN nombre VARCHAR(100) NOT NULL AFTER username,
ADD COLUMN apellido_paterno VARCHAR(100) NOT NULL AFTER nombre,
ADD COLUMN apellido_materno VARCHAR(100) NOT NULL AFTER apellido_paterno,
ADD COLUMN email VARCHAR(100) NOT NULL UNIQUE AFTER apellido_materno,
ADD COLUMN direccion TEXT AFTER id_huella";

if (mysqli_query($conn, $sql)) {
    echo "Tabla usuarios actualizada exitosamente";
} else {
    echo "Error al actualizar la tabla: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 