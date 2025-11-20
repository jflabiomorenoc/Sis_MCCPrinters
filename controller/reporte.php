<?php 
require_once ("../config/conexion.php");
require_once ("../model/Reporte.php");
$reporte = new Reporte();

switch($_GET["op"]){
    case "clientes":
        $reporte->generarExcelClientes();
        break;

    case "equipos":
        $reporte->generarExcelEquipos();
        break;

    case "contratos":
        $reporte->generarExcelContratos();
        break;

    case "tickets":
        $reporte->generarExcelTickets();
        break;
}

?>