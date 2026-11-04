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
        <style>
            /* Carga de archivos */
            .file-upload-area {
                border: 2px dashed #dee2e6;
                border-radius: 8px;
                padding: 30px 20px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s;
                background: #fff;
            }

            .file-upload-area:hover {
                border-color: #0d6efd;
                background: #f8f9ff;
            }

            .file-upload-area.dragover {
                border-color: #0d6efd;
                background: #e7f1ff;
            }

            .file-upload-area i {
                font-size: 3rem;
                color: #6c757d;
            }

            .preview-container-modal {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 12px;
                margin-top: 15px;
            }

            .preview-item-modal {
                position: relative;
                width: 100%;
                height: 140px;
                border-radius: 10px;
                overflow: hidden;
                border: 2px solid #e9ecef;
                background: #fff;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .preview-item-modal:hover {
                transform: translateY(-4px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                border-color: #0d6efd;
            }

            .preview-item-modal img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .preview-item-modal .remove-btn-modal {
                position: absolute;
                top: 8px;
                right: 8px;
                background: rgba(220, 53, 69, 0.95);
                color: white;
                border: none;
                border-radius: 50%;
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 16px;
                padding: 0;
                transition: all 0.2s ease;
                z-index: 10;
                box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
            }

            .preview-item-modal .remove-btn-modal:hover {
                background: #dc3545;
                transform: scale(1.15) rotate(90deg);
                box-shadow: 0 4px 12px rgba(220, 53, 69, 0.6);
            }

            .file-preview-doc {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 4px;
                padding: 10px;
                width: 100%;
                height: 100%;
            }

            .file-preview-doc .file-name {
                font-size: 11px;
                color: #444;
                text-align: center;
                word-break: break-all;
                max-width: 100%;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .file-preview-doc .file-size {
                font-size: 10px;
                color: #999;
            }
        </style>
        <div class="pc-container">
            <div class="pc-content"><!-- [ breadcrumb ] start -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="../Dashboard/">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="javascript: void(0)">Equipos</a></li>
                                    <li class="breadcrumb-item" aria-current="page">Lista de equipos</li>
                                </ul>
                            </div>
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h2 class="mb-0">Equipos</h2>
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
                                    <h5 class="mb-3 mb-sm-0">Lista de equipos</h5>
                                    <div>
                                        <a style="color: #fff;" id="btnNuevo" onclick="return modalNuevo();" class="btn btn-primary">Nuevo</a>
                                        <a class="btn btn-outline-success btn-reporte" data-bs-toggle="tooltip" title="Descargar reporte" href="../../controller/reporte.php?op=equipos">
                                            <i class="ph-duotone ph-microsoft-excel-logo"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="data_equipo" class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-top-0 text-center">N° Serie</th>
                                                <th class="border-top-0 text-center">Marca</th>
                                                <th class="border-top-0 text-center">Modelo</th>
                                                <th class="border-top-0 text-center">Categoría</th>
                                                <th class="border-top-0 text-center">Condición</th>
                                                <th class="border-top-0">Proveedor</th>
                                                <th class="border-top-0 text-center">Archivos</th>
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
        <?php include "modal_archivos.php"; ?>
    
    <?php
        include "../MainFooter/footer.php";
    ?>

    <script src="index.js"></script>