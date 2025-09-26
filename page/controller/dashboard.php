<?php
require_once("../config/conexion.php");
require_once("../model/Dashboard.php");
$dashboard = new Dashboard();

switch($_GET["op"]){   
    case "obtener_estadisticas_dashboard":
        $datos = $dashboard->obtener_estadisticas_dashboard();
        echo json_encode($datos[0]);
        break;
}