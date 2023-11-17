<?php
require "config/Conexion.php";

  //print_r($_SERVER['REQUEST_METHOD']);
  switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      // Consulta SQL para seleccionar datos de la tabla
$sql = "SELECT cve_entidad, desc_entidad, cve_municipio, desc_municipio, id_indicador, indicador, año, valor, unidad_medida FROM accidentes LIMIT 10";

$query = $conexion->query($sql);

if ($query->num_rows > 0) {
    $data = array();
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }
    // Devolver los resultados en formato JSON
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    echo "No se encontraron registros en la tabla.";
}

$conexion->close();
      break;


    case 'POST':
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recibir los datos del formulario HTML
        $nombre = $_POST['nombre'];
        $apodo = $_POST['apodo'];
        $tel = $_POST['tel'];
        $foto = $_POST['foto'];
     
    
        // Insertar los datos en la tabla
        $sql = "INSERT INTO maestro (nombre, apodo, tel, foto ) VALUES ('$nombre', '$apodo','$tel', '$foto')"; // Reemplaza con el nombre de tu tabla
    
        if ($conexion->query($sql) === TRUE) {
            echo "Datos insertados con éxito.";
        } else {
            echo "Error al insertar datos: " . $conexion->error;
        }
    } else {
        echo "Esta API solo admite solicitudes POST.";
    }
    
    $conexion->close();
      break;
      case 'PATCH':
        if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
          parse_str(file_get_contents("php://input"), $datos);
      
          $id_mae = $datos['id_mae'];
          $apodo = $datos['apodo'];
          $foto = $datos['foto'];
          $tel = $datos['tel'];
      
          if ($_SERVER['REQUEST_METHOD'] === 'PATCH') { // Método PATCH
              $actualizaciones = array();
              if (!empty($apodo)) {
                  $actualizaciones[] = "apodo = '$apodo'";
              }
              if (!empty($foto)) {
                  $actualizaciones[] = "foto = '$foto'";
              }
              if (!empty($tel)) {
                  $actualizaciones[] = "tel = '$tel'";
              }
      
              $actualizaciones_str = implode(', ', $actualizaciones);
              $sql = "UPDATE maestro SET $actualizaciones_str WHERE id_mae = $id_mae";
          }
      
          if ($conexion->query($sql) === TRUE) {
              echo "Registro actualizado con éxito.";
          } else {
              echo "Error al actualizar registro: " . $conexion->error;
          }
      } else {
          echo "Método de solicitud no válido.";
      }
      
      $conexion->close();
       break;

    case 'PUT':
      if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH') {
        parse_str(file_get_contents("php://input"), $datos);
    
        $id_mae = $datos['id_mae'];
        $apodo = $datos['apodo'];
        $foto = $datos['foto'];
        $tel = $datos['tel'];
    
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $sql = "UPDATE maestro SET apodo = '$apodo', foto = '$foto', tel = '$tel' WHERE id_mae = $id_mae";
        } else { // Método PATCH
            $actualizaciones = array();
            if (!empty($apodo)) {
                $actualizaciones[] = "apodo = '$apodo'";
            }
            if (!empty($foto)) {
                $actualizaciones[] = "foto = '$foto'";
            }
            if (!empty($tel)) {
                $actualizaciones[] = "tel = '$tel'";
            }
    
            $actualizaciones_str = implode(', ', $actualizaciones);
            $sql = "UPDATE maestro SET $actualizaciones_str WHERE id_mae = $id_mae";
        }
    
        if ($conexion->query($sql) === TRUE) {
            echo "Registro actualizado con éxito.";
        } else {
            echo "Error al actualizar registro: " . $conexion->error;
        }
    } else {
        echo "Método de solicitud no válido.";
    }
    
    $conexion->close();

      break;
  
      
    case 'DELETE':
      if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Procesar solicitud DELETE
        $id_mae = $_GET['id_mae'];
        $sql = "DELETE FROM maestro WHERE id_mae = $id_mae";
    
        if ($conexion->query($sql) === TRUE) {
            echo "Registro eliminado con éxito.";
        } else {
            echo "Error al eliminar registro: " . $conexion->error;
        }
    } else {
        echo "Método de solicitud no válido.";
    }
    $conexion->close();
      break;

      case 'OPTIONS':
     // Habilitar CORS para cualquier origen
header("Access-Control-Allow-Origin: *");

// Permitir métodos HTTP específicos
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, HEAD, TRACE, PATCH");

// Permitir encabezados personalizados
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Permitir credenciales
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Responder a la solicitud OPTIONS sin procesar nada más
    http_response_code(200);
    exit;
}

        
        break;
case 'HEAD':
  if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
    // Establecer encabezados de respuesta
    header('Content-Type: application/json');
    header('Custom-Header: PHP 8, HTML ');

    // Puedes establecer otros encabezados necesarios aquí

    // No es necesario enviar un cuerpo en una solicitud HEAD, por lo que no se imprime nada aquí.
} else {
    http_response_code(405); // Método no permitido
    echo 'Método de solicitud no válido';
}
  break;

  case 'TRACE':
    header("Access-Control-Allow-Origin: *");
    if ($_SERVER['REQUEST_METHOD'] === 'TRACE') {
      $response = "Solicitud TRACE recibida. Estado: 200 OK";
  } else {
      $response = "Método de solicitud no válido. Estado: 405 Método no permitido";
  }
  
  echo $response;
    break;

    case'LINK':
      $apiUrl = 'https://ejemplo.com/tu_endpoint'; // Reemplaza con la URL de tu API
      $resourceUri = '/ruta/a/tu/recurso'; // Reemplaza con la ruta de tu recurso
      $linkHeader = '<' . $resourceUri . '>; rel="link-type"'; // Define el encabezado Link
      
      $options = [
          'http' => [
              'method' => 'LINK',
              'header' => 'Link: ' . $linkHeader,
          ],
      ];
      
      $context = stream_context_create($options);
      $response = file_get_contents($apiUrl, false, $context);
      
      if ($response === false) {
          echo "Error al enviar la solicitud LINK.";
      } else {
          echo "Solicitud LINK exitosa. Respuesta del servidor: " . $response;
      }
      break;
case 'UNLINK':
    $apiUrl = 'https://ejemplo.com/tu_endpoint'; // Reemplaza con la URL de tu API
    $resourceUri = '/ruta/a/tu/recurso'; // Reemplaza con la ruta de tu recurso
    $linkHeader = '<' . $resourceUri . '>; rel="unlink"'; // Define el encabezado Link
    
    $options = [
        'http' => [
            'method' => 'UNLINK',
            'header' => 'Link: ' . $linkHeader,
        ],
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($apiUrl, false, $context);
    
    if ($response === false) {
        echo "Error al enviar la solicitud UNLINK.";
    } else {
        echo "Solicitud UNLINK exitosa. Respuesta del servidor: " . $response;
    }
    break;

     default:
       echo 'undefined request type!';
  }
?>