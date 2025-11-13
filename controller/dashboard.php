<?php
require_once("../config/conexion.php");
require_once("../model/Dashboard.php");
$dashboard = new Dashboard();

$usuario_id  = $_SESSION['id'];
$rol_usuario = $_SESSION['rol_usuario'];
        
switch($_GET["op"]){   
    case "obtener_estadisticas_dashboard":
        $datos = $dashboard->obtener_estadisticas_dashboard($usuario_id, $rol_usuario);
        echo json_encode($datos[0]);
        break;
}