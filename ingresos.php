<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/ingreso.class.php';

$_respuestas = new respuestas;
$_ingresoUsuario = new ingreso;


if($_SERVER['REQUEST_METHOD'] == "GET"){

    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $listaIngresoslUsuarios = $_ingresoUsuario->listarIngresos($pagina);
        header("Content-Type: application/json");
        echo json_encode($listaIngresoslUsuarios);
        http_response_code(200);
    }else if(isset($_GET['id'])){
        $IdUsuario = $_GET['id'];
        $datosUsuario = $_ingresoUsuario->obtenerIngreosId($IdUsuario);
        header("Content-Type: application/json");
        echo json_encode($datosUsuario);
        http_response_code(200);
    }
    
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //recibimos los datos enviados
    $postBody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $_ingresoUsuario->post($postBody);
    //delvovemos una respuesta 
     header('Content-Type: application/json');
     if(isset($datosArray["result"]["error_id"])){
         $responseCode = $datosArray["result"]["error_id"];
         http_response_code($responseCode);
     }else{
         http_response_code(200);
     }
     echo json_encode($datosArray);
    

}else if($_SERVER['REQUEST_METHOD'] == "DELETE"){

        $headers = getallheaders();
        if(isset($headers["token"]) && isset($headers["idIngreso"]) && isset($headers["idCapital"])){
            //recibimos los datos enviados por el header
            $send = [
                "token" => $headers["token"],
                "idIngreso" =>$headers["idIngreso"],
                "idCapital" =>$headers["idCapital"]
            ];
            $postBody = json_encode($send);
        }else{
            //recibimos los datos enviados
            $postBody = file_get_contents("php://input");
        }
        
        //enviamos datos al manejador
        $datosArray = $_ingresoUsuario->delete($postBody);
        //delvovemos una respuesta 
        header('Content-Type: application/json');
        if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray);
       

}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}


?>