<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class categoriasGasto extends conexion {

    private $table = "categoriasgastos";
    private $idUsuario = "";
    private $nombre = "";
    private $icono = "";
    private $token = "";
    private $idCategoriaGasto = "";
    

   
    

//ac6213b5fe79354a8fea6611a3284b5e

    /**
     * Esta función devuelve listado de categorias en gastos, espera el numero de pagina ya que solo trae 100 registros
     * @var pagina int
     * @access public
     * @return array
     */
    public function listaCategoriasGasto($pagina = 1){
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
     * Esta función devuelve todas las categorias gasto de un usuario, espera el Id 
     * @var idUsuario int
     * @access public
     * @return array
     */
    public function obtenerCategoriaGasto($id){
        $query = "SELECT * FROM " . $this->table . " WHERE categoriasgastos.idUsuario = '$id'";
        return parent::obtenerDatos($query);

    }
    

    /**
     * Esta función guarda las categorias para gastos utilizando verbo post.
     * @var nombre
     * @var icono
     * @var idUsuario
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

        $requiredFields = array('nombre', 'icono', 'idUsuario','token');
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field])) {
                return $_respuestas->error_400("El campo '$field' es obligatorio");
            }
        }

        $this->nombre = $datos['nombre'];
        $this->icono = $datos['icono'];
        $this->idUsuario = $datos['idUsuario'];
        $this->token = $datos['token'];
        $arrayToken =   $this->buscarToken();
        if($arrayToken){  
            if (!is_string($this->nombre) || !is_string($this->icono) || !is_string($this->token)) {
                return $_respuestas->error_400("Los campos nombre, icono y token deben de ser string");
            }

            if (!is_int($this->idUsuario)) {
                return $_respuestas->error_400("El campo idRol deben ser de tipo entero");
            }

            $resp = $this->insertarCategoriaGasto();
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

    private function insertarCategoriaGasto(){

        $params = array(
            //s= string , i = int
            array('type' => 's', 'value' => $this->nombre),
            array('type' => 's', 'value' => $this->icono),
            array('type' => 'i', 'value' => $this->idUsuario)
        );
        $result = $this->executeStoredProcedure('insertarCategoriaGasto', $params);
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
        }
    }
    
    
     /**
     * Esta función actualiza categoria gasto por medio de ID.
     * @var idCategoriaGasto
     * @var nombre
     * @var icono
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

        $requiredFields = array('idCategoriaGasto','nombre', 'icono','token');
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field])) {
                return $_respuestas->error_400("El campo '$field' es obligatorio");
            }
        }

         $this->nombre = $datos['nombre'];
         $this->idCategoriaGasto = $datos['idCategoriaGasto'];
         $this->icono = $datos['icono'];
         $this->token = $datos['token'];
         $arrayToken =   $this->buscarToken();

         if ( $arrayToken){
            
            if (!is_string($this->nombre) || !is_string($this->icono) || !is_string($this->token)) {
                return $_respuestas->error_400("Los campos nombre, icono y token deben de ser string");
            }

            if (!is_int($this->idCategoriaGasto)) {
                return $_respuestas->error_400("El campo idCategoriaGasto deben ser de tipo entero");
            }
            $resp = $this->modificarCategoriaGasto();
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
     * Esta función ejecuta query que actualiza registros en la tabla Categoria gasto
     * @access private
     * @return array
     */
    private function modificarCategoriaGasto(){
        $params = array(
            //s= string , i = int
            array('type' => 's', 'value' => $this->nombre),
            array('type' => 's', 'value' => $this->icono),
            array('type' => 'i', 'value' => $this->idCategoriaGasto)
            
        );
       
        $result = $this->executeStoredProcedure('editarCatergoriasGasto', $params);
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

        $requiredFields = array('idCategoriaGasto','token');
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
    
        $this->idCategoriaGasto = $datos['idCategoriaGasto'];
    
        if (!is_string($this->token)) {
            return $_respuestas->error_400("El campo token debe ser de tipo string");
        }
    
        if (!is_int($this->idCategoriaGasto)) {
            return $_respuestas->error_400("El campo idCategoriaGasto debe ser de tipo entero");
        }
    
        $resp = $this->eliminarCategoriaGasto();
    
        if ($resp) {
            $respuesta = $_respuestas->ok_200_procedimientos_almacenados('Categoría eliminada con éxito');
            return $respuesta;
        } else {
            return $_respuestas->error_500();
        }
    }

    /**
     * Esta función ejecuta query que elimina registros en la tabla categoria gasto por el ID
     * @access private
     * @return array
     */
    private function eliminarCategoriaGasto(){
        $params = array(
            // i = int
            array('type' => 'i', 'value' => $this->idCategoriaGasto)
        );
        $result = $this->executeStoredProcedure('inhabilitarCategoriaGasto', $params);
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