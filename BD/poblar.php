<?php
$host = 'localhost';
$db = 'gescon';
$user = 'root';
$pass = ''; // Si usas contraseña, colócala aquí

// Intentar conectar
$mysqli = new mysqli($host, $user, $pass, $db);

// Si falla la conexión, detener con mensaje claro
if ($mysqli->connect_error) {
    die("❌ Error de conexión a la base de datos '$db': " . $mysqli->connect_error);
}

// Función para ejecutar scripts .sql
function ejecutarSQL($mysqli, $archivo) {
    if (!file_exists($archivo)) {
        echo "❌ Archivo no encontrado: $archivo<br>";
        return;
    }

    $sql = file_get_contents($archivo);
    if ($sql === false) {
        echo "❌ No se pudo leer el archivo: $archivo<br>";
        return;
    }

    if ($mysqli->multi_query($sql)) {
        do {
            if ($resultado = $mysqli->store_result()) {
                $resultado->free();
            }
        } while ($mysqli->next_result());
        echo "✅ Ejecutado correctamente: $archivo<br>";
    } else {
        echo "❌ Error al ejecutar $archivo: " . $mysqli->error . "<br>";
    }
}

// Lista de archivos SQL
$archivos = [
    'create_tables.sql',
    'trigger.sql',
    'views.sql',
    'functions.sql',
    'poblar_datos.sql'
];

// Ejecutar en orden
foreach ($archivos as $archivo) {
    ejecutarSQL($mysqli, $archivo);
}

$mysqli->close();
?>
