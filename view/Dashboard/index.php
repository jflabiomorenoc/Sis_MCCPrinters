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
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Dashboard</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-1" id="total-contratos">0</h3>
                                <p class="text-muted mb-0">Total de contratos</p>
                            </div>
                            <div class="col-4 text-end"><i class="ti ti-file-invoice text-secondary f-36"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-1" id="contratos-vigentes">0</h3>
                                <p class="text-muted mb-0">Contratos vigentes</p>
                            </div>
                            <div class="col-4 text-end"><i class="ti ti-file-invoice text-warning f-36"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-1" id="contratos-finalizados">0</h3>
                                <p class="text-muted mb-0">Contratos finalizados</p>
                            </div>
                            <div class="col-4 text-end"><i class="ti ti-file-invoice text-success f-36"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-1" id="contratos-suspendidos">0</h3>
                                <p class="text-muted mb-0">Contratos cancelados</p>
                            </div>
                            <div class="col-4 text-end"><i class="ti ti-file-invoice text-primary f-36"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-1" id="total-tickets">0</h3>
                                <p class="text-muted mb-0">Total de tickets</p>
                            </div>
                            <div class="col-4 text-end"><i class="ti ti-ticket text-secondary f-36"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-1" id="tickets-pendientes">0</h3>
                                <p class="text-muted mb-0">Tickets pendientes</p>
                            </div>
                            <div class="col-4 text-end"><i class="ti ti-ticket text-info f-36"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-1" id="tickets-en-proceso">0</h3>
                                <p class="text-muted mb-0">Tickets en proceso</p>
                            </div>
                            <div class="col-4 text-end"><i class="ti ti-ticket text-warning f-36"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-1" id="tickets-resueltos">0</h3>
                                <p class="text-muted mb-0">Tickets resueltos</p>
                            </div>
                            <div class="col-4 text-end"><i class="ti ti-ticket text-success f-36"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "../MainFooter/footer.php";
?>

<script src="index.js"></script>