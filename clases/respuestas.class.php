<?php 

class respuestas{

    public  $response = [
        'status' => "ok",
        "result" => array()
    ];

/**
 * Funcion que devuelve error: metodo no permitido
 * @access public
 * @return array
 */
    public function error_405(){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "405",
            "error_msg" => "Metodo no permitido"
        );
        return $this->response;
    }
/**
 * Funcion que devuelve error: Datos incorrectos
 * @var string
 * @access public
 * @return array
 */
    public function error_200($valor = "Datos incorrectos"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "200",
            "error_msg" => $valor
        );
        return $this->response;
    }

/**
 * Funcion que devuelve error: Datos enviados incompletos o con formato incorrecto
 * @access public
 * @return array
 */
    public function error_400(){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "400",
            "error_msg" => "Datos enviados incompletos o con formato incorrecto"
        );
        return $this->response;
    }

/**
 * Funcion que devuelve error: Error interno del servidor
 * @var string
 * @access public
 * @return array
 */
    public function error_500($valor = "Error interno del servidor"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "500",
            "error_msg" => $valor
        );
        return $this->response;
    }

/**
 * Funcion que devuelve error: No autorizado
 * @var string
 * @access public
 * @return array
 */
    public function error_401($valor = "No autorizado"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "401",
            "error_msg" => $valor
        );
        return $this->response;
    }

/**
 * Funcion que indica que la solicitud se ha realizado correctamente.
 * @access public
 * @return array
 */
public function ok_200($valor = "Tu solicitud se ha realizado correctamente."){
    $this->response['status'] = "Ok";
    $this->response['result'] = array(
        "ok_id" => "ok_200",
        "ok_msg" => $valor
    );
    return $this->response;
}
/**
 * Funcion que indica que la solicitud se ha realizado correctamente.
 * @access public
 * @return array
 */
public function ok_200_procedimientos_almacenados($valor){
    $this->response['status'] = "Ok";
    $this->response['result'] = array(
        "ok_id" => "ok_200",
        "ok_msg" => $valor
    );
    return $this->response;
}
    
    

}

?>