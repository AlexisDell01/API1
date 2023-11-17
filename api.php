<?php
require 'config/Conexion.php';

// Verificar la conexión a la base de datos
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener el tipo de solicitud
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        // Realizar acciones según el tipo de solicitud
        switch ($action) {
            case 'promedio':
                obtenerPromedio();
                break;
            case 'repetidos':
                obtenerNumeroRepetidos();
                break;
            case 'mediana':
                obtenerMediana();
                break;
            default:
                echo "Acción no válida.";
        }
    } else {
        // Procesar solicitud GET estándar
        obtenerDatos();
    }
} else {
    echo "Método de solicitud no válido.";
}

// Cierra la conexión a la base de datos
$conexion->close();

// Función para obtener todos los datos
function obtenerDatos() {
    global $conexion;

    $sql = "SELECT cve_entidad, desc_entidad, cve_municipio, desc_municipio, id_indicador, indicador, año, valor, unidad_medida FROM accidentes";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta SQL: " . $conexion->error);
    }

    if ($resultado->num_rows > 0) {
        $data = array();
        while ($row = $resultado->fetch_assoc()) {
            $data[] = $row;
        }
        // Devolver los resultados en formato JSON
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo "No se encontraron registros en la tabla.";
    }
}

// Función para obtener el promedio de la columna "valor"
function obtenerPromedio() {
    global $conexion;

    $sql = "SELECT AVG(valor) as promedio FROM accidentes";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error al calcular el promedio: " . $conexion->error);
    }

    $promedio = $resultado->fetch_assoc()['promedio'];

    // Devolver el resultado en formato JSON
    header('Content-Type: application/json');
    echo json_encode(array('promedio' => $promedio));
}

// Función para obtener el número de datos repetidos en la columna "valor"
function obtenerNumeroRepetidos() {
    global $conexion;

    $sql = "SELECT valor, COUNT(valor) as cantidad FROM accidentes GROUP BY valor HAVING COUNT(valor) > 1";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error al contar datos repetidos: " . $conexion->error);
    }

    if ($resultado->num_rows > 0) {
        $repetidos = array();
        while ($row = $resultado->fetch_assoc()) {
            $repetidos[] = $row;
        }

        // Devolver el resultado en formato JSON
        header('Content-Type: application/json');
        echo json_encode($repetidos);
    } else {
        echo "No se encontraron datos repetidos en la columna 'valor'.";
    }
}

// Función para obtener la mediana de la columna "valor"
function obtenerMediana() {
    global $conexion;

    // Utilizando variables de usuario (@rownum) y subconsultas para calcular la mediana
    $sql = "
        SELECT AVG(valor) as mediana
        FROM (
            SELECT valor, @rownum:=@rownum+1 as rownum, @totalrows:=@rownum
            FROM (SELECT valor FROM accidentes ORDER BY valor) as ordered, (SELECT @rownum:=0) as r
        ) as ranked
        WHERE rownum IN (FLOOR((@totalrows+1)/2), FLOOR((@totalrows+2)/2))
    ";

    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error al calcular la mediana: " . $conexion->error);
    }

    $mediana = $resultado->fetch_assoc()['mediana'];

    // Devolver el resultado en formato JSON
    header('Content-Type: application/json');
    echo json_encode(array('mediana' => $mediana));
}
?>
