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
                                    <li class="breadcrumb-item"><a href="javascript: void(0)">Proveedores</a></li>
                                    <li class="breadcrumb-item" aria-current="page">Lista de proveedores</li>
                                </ul>
                            </div>
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h2 class="mb-0">Proveedores</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-sm-flex align-items-center justify-content-between">
                                    <h5 class="mb-3 mb-sm-0">Lista de proveedores</h5>
                                    <div>
                                        <a style="color: #fff;" id="btnNuevo" onclick="return modalNuevo();" class="btn btn-primary">Nuevo</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="data_proveedor" class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-top-0">Nombre</th>
                                                <th class="border-top-0 text-center">Tipo RUC</th>
                                                <th class="border-top-0 text-center">RUC</th>
                                                <th class="border-top-0 text-center">Responsable</th>
                                                <th class="border-top-0">Dirección</th>
                                                <th class="border-top-0 text-center">Estado</th>
                                                <th class="border-top-0 text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- [ Main Content ] end -->
            </div>
        </div><!-- [ Main Content ] end -->

        <?php include "modal_form.php"; ?>
    
    <?php
        include "../MainFooter/footer.php";
    ?>

    <script src="index.js"></script>