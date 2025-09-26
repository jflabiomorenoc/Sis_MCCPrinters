    <?php
        require_once("../../config/conexion.php");

        if(isset($_SESSION["id"])){

            include "../MainHead/head.php";
            include "../MainNav/nav.php";
            include "../MainHeader/header.php";
            
        }
    ?>  
        <style>
            /* Centrar checkboxes en las celdas */
            #tabla-permisos tbody td .form-check {
                display: flex;
                justify-content: center;
                align-items: center;
                margin-bottom: 0;
            }

            #tabla-permisos tbody td .form-check-input {
                margin: 0;
                transform: scale(1.2); /* Hacer checkboxes un poco más grandes */
            }

            /* Estilo para las filas de la tabla */
            #tabla-permisos tbody tr:hover {
                background-color: #f8f9fa;
            }

            /* Estilo para los botones de selección masiva */
            .btn-group-sm .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            /* Espaciado mejorado para el header de la tarjeta */
            .card-header {
                padding: 1rem 1.25rem;
                background-color: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
            }

            /* Estilo para los encabezados de columna */
            #tabla-permisos thead th {
                border-bottom: 2px solid #dee2e6;
                padding: 0.75rem;
                vertical-align: middle;
            }

            /* Ancho fijo para las columnas de permisos */
            #tabla-permisos th:not(:first-child),
            #tabla-permisos td:not(:first-child) {
                width: 80px;
                min-width: 80px;
            }

            /* Estilo responsive */
            @media (max-width: 768px) {
                .btn-group {
                    flex-wrap: wrap;
                }
                
                .btn-group .btn {
                    margin-bottom: 0.25rem;
                }
                
                .card-header {
                    flex-direction: column;
                    gap: 1rem;
                }
                
                .card-header .btn-group {
                    justify-content: center;
                }
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
                                    <li class="breadcrumb-item"><a href="javascript: void(0)">Perfiles</a></li>
                                    <li class="breadcrumb-item" aria-current="page">Lista de perfiles</li>
                                </ul>
                            </div>
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h2 class="mb-0">Perfiles</h2>
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
                                    <h5 class="mb-3 mb-sm-0">Lista de perfiles</h5>
                                    <div>
                                        <a style="color: #fff;" onclick="return modalNuevo();" class="btn btn-primary">Nuevo</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="data_perfil" class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th></th>
                                                <th class="border-top-0">Nombre</th>
                                                <th class="border-top-0 text-center">Usuarios</th>
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