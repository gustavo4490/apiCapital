<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class productos extends conexion {

    private $table = "productos";
    private $idProducto = "";
    private $nombreProducto = "";
    private $marcaProducto = "";
    private $precioProducto = "";
    private $existenciaProducto = "";
    private $token = "";
   
    

//a4751ee43df940eab20dac9eeb8f7318

    /**
     * Esta función devuelve listado de productos, espera el numero de pagina ya que solo trae 100 registros
     * @var pagina int
     * @access public
     * @return array
     */
    public function listaProductos($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT idProducto,NombreProducto,marca,precio,existecia FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    /**
     * Esta función devuelve la informacion de un producto, espera el Id 
     * @var idProducto int
     * @access public
     * @return array
     */
    public function obtenerProducto($id){
        $query = "SELECT * FROM " . $this->table . " WHERE idProducto = '$id'";
        return parent::obtenerDatos($query);

    }

    /**
     * Esta función guarda productos utilizando verbo post.
     * @var nombre
     * @var marca
     * @var precio
     * @var existencia
     * @var token
     * @access public
     * @return array
     */
    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        
        if(!isset($datos['token'])){
                return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){

                if(!isset($datos['nombre']) || !isset($datos['marca']) || !isset($datos['precio'])|| !isset($datos['existencia'])){
                    return $_respuestas->error_400();
                }else{
                    
                    $this->nombreProducto = $datos['nombre'];
                    $this->marcaProducto = $datos['marca'];
                    $this->precioProducto = $datos['precio'];
                    $this->existenciaProducto = $datos['existencia'];

                    $resp = $this->insertarProductos();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idProducto" => $resp
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }


       

    }


    /**
     * Esta función ejecuta query que inserta registros a la tabla productos
     * @access private
     * @return array
     */
    private function insertarProductos(){
        $query = "INSERT INTO " . $this->table . " (nombreProducto,marca,precio,existecia)
        values
        ('" . $this->nombreProducto . "','" . $this->marcaProducto . "','" . $this->precioProducto ."','" . $this->existenciaProducto . "')"; 
        $resp = parent::nonQueryId($query);
        if($resp){
             return $resp;
        }else{
            return 0;
        }
    }
    
     /**
     * Esta función actualizar productos por medio de ID.
     * @var idProducto
     * @var nombre
     * @var marca
     * @var precio
     * @var existencia
     * @var token
     * @access public
     * @return array
     */
    public function put($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['idProducto']) || !isset($datos['marca']) || !isset($datos['precio'])|| !isset($datos['existencia'])){
                    return $_respuestas->error_400();
                }else{
                    $this->idProducto = $datos['idProducto'];
                    $this->nombreProducto = $datos['nombre'];
                    $this->marcaProducto = $datos['marca'];
                    $this->precioProducto = $datos['precio'];
                    $this->existenciaProducto = $datos['existencia'];
                    
        
                    $resp = $this->modificarProducto();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idProducto" => $this->idProducto
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }


    }


    /**
     * Esta función ejecuta query que actualiza registros en la tabla productos
     * @access private
     * @return array
     */
    private function modificarProducto(){
        $query = "UPDATE " . $this->table . " SET nombreProducto ='" . $this->nombreProducto . "',marca = '" . $this->marcaProducto . "', precio = '" . $this->precioProducto . "', existecia = '" .
        $this->existenciaProducto . 
         "' WHERE idProducto = '" . $this->idProducto . "'"; 
        $resp = parent::nonQuery($query);
        if($resp >= 1){
             return $resp;
        }else{
            return 0;
        }
    }


     /**
     * Esta función elimina productos por medio de ID.
     * @var idProducto
     * @access public
     * @return array
     */
    public function delete($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){

                if(!isset($datos['idProducto'])){
                    return $_respuestas->error_400();
                }else{
                    $this->idProducto = $datos['idProducto'];
                    $resp = $this->eliminarProducto();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idProducto" => $this->idProducto
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }



     
    }


    /**
     * Esta función ejecuta query que elimina registros en la tabla productos por el ID
     * @access private
     * @return array
     */
    private function eliminarProducto(){
        $query = "DELETE FROM " . $this->table . " WHERE idProducto= '" . $this->idProducto . "'";
        $resp = parent::nonQuery($query);
        if($resp >= 1 ){
            return $resp;
        }else{
            return 0;
        }
    }

    
    /**
     * Esta función busca si el token existe y si esta activo 
     * @access private
     * @return array
     */
    private function buscarToken(){
        
        $query = "SELECT  idToken,idUsuario,Estado from usuariostoken WHERE Token = '" . $this->token . "' AND Estado = 'Activo';";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }


    private function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuariostoken SET fecha = '$date' WHERE idToken = '$tokenid' ";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }



}





?>