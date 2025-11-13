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
            .dz-custom-style {
                border: 2px dashed #ced4da; /* Color del borde */
                height: 230px;
                background-color: #ffffff; /* Fondo blanco */
                border-radius: 12px; /* Bordes redondeados */
                padding: 20px; /* Espaciado interno */
                text-align: center; /* Centrar contenido */
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.05); /* Sombra ligera */
                transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
            }

            /* Efecto hover en el dropzone */
            .dz-custom-style:hover {
                border-color: #007bff; /* Cambiar el borde a azul */
                box-shadow: 0px 6px 15px rgba(0, 123, 255, 0.1); /* Sombras más pronunciadas */
            }

            /* Estilo cuando arrastras un archivo sobre el área */
            .dz-custom-style.dz-drag-hover {
                background-color: #f0f8ff; /* Fondo suave azul claro */
                border-color: #17a2b8; /* Cambiar color del borde al arrastrar */
                box-shadow: 0px 6px 20px rgba(23, 162, 184, 0.15); /* Efecto de sombra más fuerte */
            }

            /* Estilo del icono de subida */
            .upload-icon {
                font-size: 48px;
                color: #007bff;
                margin-bottom: 10px;
                transition: color 0.3s ease;
            }

            /* Estilo del mensaje de subir archivos */
            .dz-message p {
                font-size: 15px;
                color: #6c757d;
                margin: 0;
                font-family: 'Helvetica Neue', sans-serif;
                font-weight: 500;
                transition: color 0.3s ease;
            }

            /* Cambiar el color del mensaje y el icono al arrastrar */
            .dz-custom-style.dz-drag-hover .upload-icon,
            .dz-custom-style.dz-drag-hover p {
                color: #17a2b8;
            }


            /* Estilo del enlace de eliminación */
            .dz-remove {
                display: block;
                font-size: 14px;
                color: #ff4d4f; /* Rojo elegante */
                margin-top: 5px;
                cursor: pointer;
                text-decoration: none;
                transition: color 0.3s ease;
            }

            /* Cambiar el color al pasar sobre el enlace de eliminar */
            .dz-remove:hover {
                color: #ff0000; /* Más visible al pasar sobre */
                text-decoration: underline;
            }

            /* Progreso de subida */
            .dz-progress {
                background-color: #007bff; /* Color del progreso de subida */
                height: 6px;
                border-radius: 4px;
                margin-top: 10px;
                transition: width 0.4s ease;
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
                                    <li class="breadcrumb-item"><a href="javascript: void(0)">Usuarios</a></li>
                                    <li class="breadcrumb-item" aria-current="page">Lista de usuarios</li>
                                </ul>
                            </div>
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h2 class="mb-0">Usuarios</h2>
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
                                    <h5 class="mb-3 mb-sm-0">Lista de usuarios</h5>
                                    <div>
                                        <a style="color: #fff;" id="btnNuevo" onclick="return modalNuevo();" class="btn btn-primary">Nuevo</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="data_usuario" class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-top-0">Nombre</th>
                                                <th class="border-top-0 text-center">Contacto</th>
                                                <th class="border-top-0 text-center">Usuario</th>
                                                <th class="border-top-0 text-center">Ultimo acceso</th>
                                                <th class="border-top-0 text-center">Estado</th>
                                                <th class="border-top-0 text-center">Perfil</th>
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
        <?php include "modal_asignar.php"; ?>
    
    <?php
        include "../MainFooter/footer.php";
    ?>

    <script src="index.js"></script>