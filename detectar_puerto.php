<?php
// Función para listar los puertos COM disponibles
function listarPuertosCOM() {
    $puertos = [];
    exec('mode', $output);
    
    foreach($output as $line) {
        if(preg_match('/^COM\d+/', $line, $matches)) {
            $puertos[] = $matches[0];
        }
    }
    
    return $puertos;
}

// Función para probar un puerto
function probarPuerto($puerto) {
    $fp = @fopen($puerto, 'r+');
    if($fp) {
        fclose($fp);
        return true;
    }
    return false;
}

echo "Buscando puertos COM disponibles...\n";
$puertos = listarPuertosCOM();

if(empty($puertos)) {
    echo "No se encontraron puertos COM\n";
    echo "Verifica que el ESP32 esté conectado\n";
    exit;
}

echo "Puertos encontrados:\n";
foreach($puertos as $puerto) {
    echo "- $puerto\n";
}

echo "\nProbando puertos...\n";
foreach($puertos as $puerto) {
    if(probarPuerto($puerto)) {
        echo "Puerto $puerto disponible\n";
    } else {
        echo "Puerto $puerto no disponible\n";
    }
}

echo "\nPara usar un puerto específico, modifica el archivo serial_reader.php\n";
echo "y cambia la línea: \$port = 'COM5'; por el puerto que quieras usar\n";
?> 