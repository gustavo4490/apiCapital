<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class productos extends conexion {

    private $table = "usuarios";
    private $idUsuario = "";
    private $nombre = "";
    private $apellido = "";
    private $correo = "";
    private $password = "";
    private $estado = "";
    private $idRol = "";
    private $token = "";
    
   
    

//ac6213b5fe79354a8fea6611a3284b5e

    /**
     * Esta función devuelve listado de usuarios, espera el numero de pagina ya que solo trae 100 registros
     * @var pagina int
     * @access public
     * @return array
     */
    public function listaUsuarios($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT idUsuario,nombre,apellido,correo,estado,idRol FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    /**
     * Esta función devuelve la informacion de un usuario, espera el Id 
     * @var idUsuario int
     * @access public
     * @return array
     */
    public function obtenerUsuario($id){
        $query = "SELECT idUsuario,nombre,apellido,correo,estado,idRol FROM " . $this->table . " WHERE idUsuario = '$id'";
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

                if(!isset($datos['nombre']) || !isset($datos['apellido']) || !isset($datos['correo'])|| !isset($datos['password'])|| !isset($datos['estado'])|| !isset($datos['idRol']) ){
                    return $_respuestas->error_400();
                }else{
                    
                    $this->nombre = $datos['nombre'];
                    $this->apellido = $datos['apellido'];
                    $this->correo = $datos['correo'];
                    $this->password = $this->encriptar( $datos['password']);
                    $this->estado = $datos['estado'];
                    $this->idRol = $datos['idRol'];

                    $resp = $this->insertarUsuarios();
                    if($resp){
                        $respuesta = $_respuestas->ok_200_procedimientos_almacenados('Datos almacenados correctamente');
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

    private function insertarUsuarios(){

        $params = array(
            //s= string , i = int
            array('type' => 's', 'value' => $this->nombre),
            array('type' => 's', 'value' => $this->apellido),
            array('type' => 's', 'value' => $this->correo),
            array('type' => 's', 'value' => $this->password),
            array('type' => 'i', 'value' => $this->estado),
            array('type' => 'i', 'value' => $this->idRol)
        );
        $result = $this->executeStoredProcedure('insertarUsuarios', $params);
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
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
        // SELECT idToken,idUsuario,estado from usariostoken WHERE Token = 'ac6213b5fe79354a8fea6611a3284b5e' AND estado = 'Activo';

        $query = "SELECT idToken,idUsuario,estado from usariostoken WHERE Token = '" . $this->token . "' AND estado = 'Activo';";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }


   
}





?>