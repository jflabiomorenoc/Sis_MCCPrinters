<?php
require_once("../config/conexion.php");
require_once("../model/Modulo.php");

$modulo = new Modulo();

switch($_GET["op"]) {
    
    case "listar_modulos_permisos":
        $perfil_id = isset($_POST['perfil_id']) && !empty($_POST['perfil_id']) ? $_POST['perfil_id'] : null;
        $datos = $modulo->obtener_modulos_con_permisos($perfil_id);
        echo json_encode($datos);
        break; 
    
    case "listar_modulos":
        $datos = $modulo->listar_modulos();
        echo json_encode($datos);
        break;
}
?>