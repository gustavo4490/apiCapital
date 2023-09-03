<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class ingreso extends conexion {

    private $table = "ingresosusuario";

    private $cantidad = "";
    private $idCategoria = "";
    private $fecha = "";
    private $idUsuario = "";
    private $idCapital = "";
    private $token = "";
    private $idIngreso = "";
    

   
    

//ac6213b5fe79354a8fea6611a3284b5e

    /**
     * Esta función devuelve listado de ingresos, espera el numero de pagina ya que solo trae 100 registros
     * @var pagina int
     * @access public
     * @return array
     */
    public function listarIngresos($pagina = 1){
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
     * Esta función devuelve todos los ingresos de un usuario, espera el Id 
     * @var idUsuario int
     * @access public
     * @return array
     */
    public function obtenerIngreosId($id){
        $query = "SELECT * FROM " . $this->table . " WHERE ingresosusuario.idUsuario = '$id'";
        return parent::obtenerDatos($query);

    }
    

    /**
     * Esta función guarda los ingresos utilizando el verbo post.
     * @var cantidad
     * @var idCategoria
     * @var fecha
     * @var idUsuario
     * @var idCapital
     * @var token
     * @access public
     * @return array
     */
   public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if ($datos === null) {
            return $_respuestas->error_400("JSON inválido");
        }

        $requiredFields = array('cantidad', 'idCategoria', 'fecha','idUsuario','idCapital','token');
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field])) {
                return $_respuestas->error_400("El campo '$field' es obligatorio");
            }
        }

        $this->cantidad = $datos['cantidad'];
        $this->idCategoria = $datos['idCategoria'];
        $this->fecha = $datos['fecha'];
        $this->idUsuario = $datos['idUsuario'];
        $this->idCapital = $datos['idCapital'];
        $this->token = $datos['token'];

        $arrayToken =   $this->buscarToken();
        if($arrayToken){  
            if (!is_string($this->fecha) || !is_string($this->token)) {
                return $_respuestas->error_400("Los campos fecha y token deben de ser string");
            }

            if (!is_int($this->idUsuario) ||!is_int($this->idCapital) ||!is_int($this->idCategoria)) {
                return $_respuestas->error_400("El campo idUsuario, idCategoria y idCapital deben ser de tipo entero");
            }
            if (!is_float($this->cantidad)) {
                return $_respuestas->error_400("El campo cantidad deben ser de tipo float");
            }

            $resp = $this->insertarIngresos();
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
     * @var cantidad
     * @var idCategoria
     * @var fecha
     * @var idUsuario
     * @var idCapital
     * @access private
     * @return boolen
     */

    private function insertarIngresos(){

        $params = array(
            //s= string , i = int
            array('type' => 's', 'value' => $this->cantidad),
            array('type' => 's', 'value' => $this->idCategoria),
            array('type' => 's', 'value' => $this->fecha),
            array('type' => 'i', 'value' => $this->idUsuario),
            array('type' => 'i', 'value' => $this->idCapital)
        );
        $result = $this->executeStoredProcedure('insertarIngreso2', $params);
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
        }
    }
    
    

     /**
     * Esta función elimina categorias Gasto por medio de ID.
     * @var idCategoriaGasto
     * @access public
     * @return array
     */
    public function delete($json) {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if ($datos === null) {
            return $_respuestas->error_400("JSON inválido");
        }

        $requiredFields = array('idCapital','idIngreso','token');
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field])) {
                return $_respuestas->error_400("El campo '$field' es obligatorio");
            }
        }
    
        $this->token = $datos['token'];
        $arrayToken = $this->buscarToken();
    
        if (!$arrayToken) {
            return $_respuestas->error_401("El Token que envió es inválido o ha caducado");
        }
        $this->idCapital = $datos['idCapital'];
        $this->idIngreso = $datos['idIngreso'];
        
    
        if (!is_string($this->token)) {
            return $_respuestas->error_400("El campo token debe ser de tipo string");
        }
    
        if (!is_int($this->idCapital )|| !is_int($this->idIngreso )) {
            return $_respuestas->error_400("El campo idCapital y idIngreso debe ser de tipo entero");
        }
    
        $resp = $this->eliminarIngreso();
    
        if ($resp) {
            $respuesta = $_respuestas->ok_200_procedimientos_almacenados('ingreso eliminado con éxito');
            return $respuesta;
        } else {
            return $_respuestas->error_500();
        }
    }

    /**
     * Esta función ejecuta query que elimina registros en la tabla ingresosUsuario por el ID
     * @access private
     * @return array
     */
    private function eliminarIngreso(){
        $params = array(
            // i = int
            array('type' => 'i', 'value' => $this->idCapital),
            array('type' => 'i', 'value' => $this->idIngreso)
        );
        $result = $this->executeStoredProcedure('EliminarIngreso', $params);
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