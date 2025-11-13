<?php
require_once("../../config/conexion.php");

// Verificar sesión ANTES de cualquier output
if(!isset($_SESSION["id"])){
    header("Location: " . Conectar::ruta());
    exit;
}

// Solo si la sesión existe, incluir los archivos
include "../MainHead/head.php";
include "../MainNav/nav.php";
include "../MainHeader/header.php";
?>
        <div class="pc-container">
            <div class="pc-content"><!-- [ breadcrumb ] start -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="../Dashboard/">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="../Contrato/">Contratos</a></li>
                                    <li class="breadcrumb-item" aria-current="page" id="lblLiContrato"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div>
                                        <h4 id="lblNumContrato"></h4>
                                        <div class="mt-2">
                                            <span class="badge rounded-pill" id="lblEstado"></span>
                                        </div>
                                    </div>
                                    <div class="dropdown" id="dropdownAccion">
                                        <a class="avtar avtar-s btn-link-secondary dropdown-toggle arrow-none" style="cursor: pointer;" 
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ti ti-dots-vertical f-18"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" style="cursor: pointer;" id="btnEditarContrato" onclick="editarContrato()">
                                                <i class="ti ti-pencil me-2"></i>Editar
                                            </a>
                                            <a class="dropdown-item text-danger" style="cursor: pointer; display:none;" id="btnCancelarContrato" onclick="estadoContrato('cancelado')">
                                                <i class="ti ti-circle-x me-2"></i>Cancelar
                                            </a>
                                            <a class="dropdown-item text-success" style="cursor: pointer; display:none;" id="btnFinalizarContrato" onclick="estadoContrato('finalizado')">
                                                <i class="ti ti-check me-2"></i>Finalizar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <h6>INICIO</h6>
                                            <div id="lblInicio"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <h6>CULMINACIÓN</h6>
                                            <div id="lblFin"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <h6>CLIENTE</h6>
                                            <div id="lblCliente"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <h6>TÉCNICO RESPONSABLE</h6>
                                            <div id="lblTecnico"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Equipos del Contrato -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Equipos asignados</h5>
                                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" style="cursor: pointer; display:none !important;" id="btnNuevo">
                                    <i class="ti ti-plus me-1"></i>Agregar Equipo
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="tabla-equipos">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Serie</th>
                                                <th class="text-center">Marca/Modelo</th>
                                                <th class="text-center">IP Asignada</th>
                                                <th class="text-center">Ubicación</th>
                                                <th class="text-center">Contador BN (Inicial)</th>
                                                <th class="text-center">Contador Color (Inicial)</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php include "modal_equipo.php"; ?>
    
<?php
include "../MainFooter/footer.php";
?>

<script src="detalle.js"></script>