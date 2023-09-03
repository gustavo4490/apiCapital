<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class capitalUsuarios extends conexion {

    private $table = "capitalusuarios";
    private $idUsuario = "";
    private $nombre = "";
    private $saldo = "";
    private $fecha = "";
    private $token = "";
    private $idCapital = "";
    
    private $idCategoriaGasto = "";
    private $icono = "";

   
    

//ac6213b5fe79354a8fea6611a3284b5e

    /**
     * Esta función devuelve listado de capital usuarios, espera el numero de pagina ya que solo trae 100 registros
     * @var pagina int
     * @access public
     * @return array
     */
    public function listaCapitalUsuario($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT * FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    /**
     * Esta función devuelve todos los capitales  de un usuario, espera el Id 
     * @var idUsuario int
     * @access public
     * @return array
     */
    public function obtenerCapitalUsuario($id){
        $query = "SELECT * FROM " . $this->table . " WHERE capitalusuarios.idUsuario = '$id'";
        return parent::obtenerDatos($query);

    }
    

    /**
     * Esta función guarda capital utilizando verbo post.
     * @var nombre
     * @var saldo
     * @var idUsuario
     * @var fecha
     * @access public
     * @return array
     */
   public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if ($datos === null) {
            return $_respuestas->error_400("JSON inválido");
        }

        $requiredFields = array('nombre', 'saldo', 'idUsuario','fecha','token');
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field])) {
                return $_respuestas->error_400("El campo '$field' es obligatorio");
            }
        }

        $this->nombre = $datos['nombre'];
        $this->saldo = $datos['saldo'];
        $this->idUsuario = $datos['idUsuario'];

        $this->fecha = $datos['fecha'];
        $this->token = $datos['token'];
        $arrayToken =   $this->buscarToken();
        if($arrayToken){  
            if (!is_string($this->nombre) || !is_string($this->saldo) || !is_string($this->token)) {
                return $_respuestas->error_400("Los campos nombre, saldo y token deben de ser string");
            }
            if (!is_string($this->fecha) && !strtotime($this->fecha)) {
                return $_respuestas->error_400("El campo fecha debe de ser una fecha válida");
            }

            if (!is_int($this->idUsuario)) {
                return $_respuestas->error_400("El campo idUsuario deben ser de tipo entero");
            }

            $resp = $this->insertarCapital();
            if ($resp) {
                $respuesta = $_respuestas->ok_200_procedimientos_almacenados('Datos almacenados correctamente');
                return $respuesta;
            } else {
                return $_respuestas->error_500();
            }
        }
        else{
            return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
        }
    }


     /**
     * Esta función inserta las nuevas categorias para gasto.
     * @var nombre
     * @var icono
     * @var idUsuario
     * @access private
     * @return boolen
     */

    private function insertarCapital(){

        $params = array(
            //s= string , i = int
            array('type' => 's', 'value' => $this->nombre),
            array('type' => 's', 'value' => $this->saldo),
            array('type' => 'i', 'value' => $this->idUsuario),
            array('type' => 's', 'value' => $this->fecha)
        );
        $result = $this->executeStoredProcedure('insertarCapitalUsuarios', $params);
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
        }
    }
    
    
     /**
     * Esta función actualiza capital por medio de ID.
     * @var idCapital
     * @var nombre
     * @var token
     * @access public
     * @return array
     */
    public function put($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if ($datos === null) {
            return $_respuestas->error_400("JSON inválido");
        }

        $requiredFields = array('idCapital','nombre','token');
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field])) {
                return $_respuestas->error_400("El campo '$field' es obligatorio");
            }
        }

         $this->nombre = $datos['nombre'];
         $this->idCapital = $datos['idCapital'];
         $this->token = $datos['token'];
         $arrayToken =   $this->buscarToken();

         if ( $arrayToken){
            
            if (!is_string($this->nombre) || !is_string($this->token)) {
                return $_respuestas->error_400("Los campos nombre y token deben de ser string");
            }

            if (!is_int($this->idCapital)) {
                return $_respuestas->error_400("El campo idCapital deben ser de tipo entero");
            }
            $resp = $this->modificarCapital();
            if ($resp) {
                $respuesta = $_respuestas->ok_200_procedimientos_almacenados('Datos almacenados correctamente');
                return $respuesta;
            } else {
                return $_respuestas->error_500();
            }

         }else{
             return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
         }

    }


    /**
     * Esta función ejecuta query que actualiza registros en la tabla capital
     * @access private
     * @return array
     */
    private function modificarCapital(){
        $params = array(
            //s= string , i = int
            array('type' => 'i', 'value' => $this->idCapital),
            array('type' => 's', 'value' => $this->nombre)
            
        );
       
        $result = $this->executeStoredProcedure('editarCapitalUsuarios', $params);
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
        }
    }


     /**
     * Esta función que inhabilita capital por medio de ID.
     * @var idCapital
     * @access public
     * @return array
     */
    public function delete($json) {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if ($datos === null) {
            return $_respuestas->error_400("JSON inválido");
        }

        $requiredFields = array('idCapital','token');
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field])) {
                return $_respuestas->error_400("El campo '$field' es obligatorio");
            }
        }
    
        $this->token = $datos['token'];
        
        if (!is_string($this->token)) {
            return $_respuestas->error_400("El campo token debe ser de tipo string");
        }
        
        $arrayToken = $this->buscarToken();
    
        if (!$arrayToken) {
            return $_respuestas->error_401("El Token que envió es inválido o ha caducado");
        }
    
        $this->idCapital = $datos['idCapital'];
        if (!is_int($this->idCapital)) {
            return $_respuestas->error_400("El campo idCapital debe ser de tipo entero");
        }

        $resp = $this->inhabilitarCapital();
    
        if ($resp) {
            $respuesta = $_respuestas->ok_200_procedimientos_almacenados('Capital inhabilitado con éxito');
            return $respuesta;
        } else {
            return $_respuestas->error_500();
        }
    }

    /**
     * Esta función ejecuta query que inhabilita en la tabla capital por el ID
     * @access private
     * @return array
     */
    private function inhabilitarCapital(){
        $params = array(
            // i = int
            array('type' => 'i', 'value' => $this->idCapital)
        );
        $result = $this->executeStoredProcedure('inhabilitarCapitaUsuario', $params);
        echo $result;
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
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