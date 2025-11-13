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
                                    <li class="breadcrumb-item"><a href="../Cliente/">Clientes</a></li>
                                    <li class="breadcrumb-item" aria-current="page" id="lblLiCliente"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-sm-12">
                                    <div class="d-flex align-items-center">
                                        <h4 id="lblNomCliente"></h4>
                                    </div>
                                    <h5><span class="text-muted f-w-400" id="lblRuc"></span></h5>
                                    <h5>
                                        <span class="badge rounded-pill mt-2" id="lblEstado"></span>
                                        <span class="badge rounded-pill mt-2" id="lblTipo"></span>
                                    </h5>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header" style="padding-top: 1.5%; padding-bottom: 1.5%;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0">Direcciones</h5>
                                    <div>
                                        <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 btn-crear" id="btnNuevaDireccion">
                                            <i class="ph-duotone ph-plus-circle"></i> 
                                            Agregar dirección
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3" id="contenedorDirecciones">
                                    <!-- Aquí se cargarán las direcciones dinámicamente -->
                                    <div class="col-12 text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando direcciones...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- [ Main Content ] end -->
            </div>
        </div>

        <?php include "modal_direccion.php"; ?>
        <?php include "modal_contacto.php"; ?>
    
    <?php
        include "../MainFooter/footer.php";
    ?>

    <script src="detalle.js"></script>