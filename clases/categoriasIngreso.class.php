<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class categoriasIngreso extends conexion {

    private $table = "categoriasingreso";
    private $idUsuario = "";
    private $nombre = "";
    private $icono = "";
    private $token = "";
    private $idCategoriaIngreso = "";
    
   
    

//ac6213b5fe79354a8fea6611a3284b5e

    /**
     * Esta función devuelve listado de categorias en ingresos, espera el numero de pagina ya que solo trae 100 registros
     * @var pagina int
     * @access public
     * @return array
     */
    public function listaCategoriasIngreso($pagina = 1){
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
     * Esta función devuelve todas las categorias de un usuario, espera el Id 
     * @var idUsuario int
     * @access public
     * @return array
     */
    public function obtenerCategoriaIngreso($id){
        $query = "SELECT * FROM " . $this->table . " WHERE categoriasingreso.idUsuario = '$id'";
        return parent::obtenerDatos($query);

    }
    

    /**
     * Esta función guarda las categorias para ingresos utilizando verbo post.
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

            $resp = $this->insertarCategoriaIngreso();
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
     * Esta función inserta las nuevas categorias para ingresos.
     * @var nombre
     * @var icono
     * @var idUsuario
     * @access private
     * @return boolen
     */

    private function insertarCategoriaIngreso(){

        $params = array(
            //s= string , i = int
            array('type' => 's', 'value' => $this->nombre),
            array('type' => 's', 'value' => $this->icono),
            array('type' => 'i', 'value' => $this->idUsuario)
        );
        $result = $this->executeStoredProcedure('insertarCategoriasIngreso', $params);
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
        }
    }
    
    
     /**
     * Esta función actualiza categoria ingreso por medio de ID.
     * @var idCategoriaIngreso
     * @var nombre
     * @var icono
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
                $requiredFields = array('idCategoriaIngreso','nombre', 'icono');
                foreach ($requiredFields as $field) {
                    if (!isset($datos[$field])) {
                        return $_respuestas->error_400("El campo '$field' es obligatorio");
                    }
                }
                $this->nombre = $datos['nombre'];
                $this->idCategoriaIngreso = $datos['idCategoriaIngreso'];
                $this->icono = $datos['icono'];

                if (!is_string($this->nombre) || !is_string($this->icono) || !is_string($this->token)) {
                    return $_respuestas->error_400("Los campos nombre, icono y token deben de ser string");
                }
    
                if (!is_int($this->idCategoriaIngreso)) {
                    return $_respuestas->error_400("El campo idRol deben ser de tipo entero");
                }

                $resp = $this->modificarCategoriaIngreso();
                if ($resp) {
                    $respuesta = $_respuestas->ok_200_procedimientos_almacenados('Datos actualizados correctamente');
                    return $respuesta;
                } else {
                    return $_respuestas->error_500();
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }


    }


    /**
     * Esta función ejecuta query que actualiza registros en la tabla Categoria ingresos
     * @access private
     * @return array
     */
    private function modificarCategoriaIngreso(){
        $params = array(
            //s= string , i = int
            array('type' => 'i', 'value' => $this->idCategoriaIngreso),
            array('type' => 's', 'value' => $this->nombre),
            array('type' => 's', 'value' => $this->icono),
            
        );
        $result = $this->executeStoredProcedure('editarCatergoriasIngreso', $params);
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
        }
    }


     /**
     * Esta función elimina categorias ingreso por medio de ID.
     * @var idCategoriaIngreso
     * @access public
     * @return array
     */
    public function delete($json) {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if ($datos === null) {
            return $_respuestas->error_400("JSON inválido");
        }

        $requiredFields = array('idCategoriaIngreso','token');
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
    
        $this->idCategoriaIngreso = $datos['idCategoriaIngreso'];
    
        if (!is_string($this->token)) {
            return $_respuestas->error_400("El campo token debe ser de tipo string");
        }
    
        if (!is_int($this->idCategoriaIngreso)) {
            return $_respuestas->error_400("El campo idCategoriaIngreso debe ser de tipo entero");
        }
    
        $resp = $this->eliminarCategoriaIngreso();
    
        if ($resp) {
            $respuesta = $_respuestas->ok_200_procedimientos_almacenados('Categoría eliminada con éxito');
            return $respuesta;
        } else {
            return $_respuestas->error_500();
        }
    }

    /**
     * Esta función ejecuta query que elimina registros en la tabla categoria ingreso por el ID
     * @access private
     * @return array
     */
    private function eliminarCategoriaIngreso(){
        $params = array(
            // i = int
            array('type' => 'i', 'value' => $this->idCategoriaIngreso)
        );
        $result = $this->executeStoredProcedure('inhabilitarCategoriaIngreso', $params);
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