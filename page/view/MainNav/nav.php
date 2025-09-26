    <body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light"><!-- [ Pre-loader ] start -->
        <div class="loader-bg">
            <div class="loader-track">
                <div class="loader-fill"></div>
            </div>
        </div>
        <nav class="pc-sidebar">
            <div class="navbar-wrapper">
                <div class="m-header">
                    <a href="../dashboard/index.html" class="b-brand text-primary">
                        <img src="../../../assets/images/logo.jpg" class="img-fluid" alt="logo">
                    </a>
                </div>
                <div class="navbar-content">
                    <div class="card pc-user-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0"><img src="../../../assets/images/user/avatar-1.jpg" alt="user-image" class="user-avtar wid-45 rounded-circle"></div>
                                <div class="flex-grow-1 ms-3 me-2">
                                    <h6 class="mb-0"><?php echo $_SESSION["usuario"]; ?></h6>
                                    <small><?php echo $_SESSION["desc_rol"]; ?></small>
                                </div>
                                <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-sort-outline"></use>
                                    </svg>
                                </a>
                            </div>
                            <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                                <div class="pt-3">
                                    <a href="#!"><i class="ti ti-user"></i> <span>Mi cuenta</span> </a>
                                    <a href="#!"><i class="ti ti-settings"></i> <span>Configuración</span> </a>
                                    <a href="#!"><i class="ti ti-lock"></i> <span>Bloquear</span> </a>
                                    <a href="#!"><i class="ti ti-power"></i> <span>Salir</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="pc-navbar">
                        <li class="pc-item pc-caption"><label>Navigation</label></li>

                        <li class="pc-item">
                            <a href="../Dashboard/" class="pc-link">
                                <span class="pc-micon">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-status-up"></use>
                                    </svg>
                                </span>
                                <span class="pc-mtext">Dashboard</span>
                            </a>
                        </li>

                        <li class="pc-item">
                            <a href="../Contrato/" class="pc-link">
                                <span class="pc-micon">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-bill"></use>
                                    </svg>
                                </span>
                                <span class="pc-mtext">Contratos</span>
                            </a>
                        </li>

                        <li class="pc-item">
                            <a href="../Ticket/" class="pc-link">
                                <span class="pc-micon">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-flag"></use>
                                    </svg>
                                </span>
                                <span class="pc-mtext">Tickets</span>
                            </a>
                        </li>

                        <li class="pc-item">
                            <a href="../Cliente/" class="pc-link">
                                <span class="pc-micon">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-user"></use>
                                    </svg>
                                </span>
                                <span class="pc-mtext">Clientes</span>
                            </a>
                        </li>

                        <li class="pc-item">
                            <a href="../Proveedor/" class="pc-link">
                                <span class="pc-micon">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-user"></use>
                                    </svg>
                                </span>
                                <span class="pc-mtext">Proveedores</span>
                            </a>
                        </li>

                        <li class="pc-item">
                            <a href="../Usuario/" class="pc-link">
                                <span class="pc-micon">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-user-square"></use>
                                    </svg>
                                </span>
                                <span class="pc-mtext">Usuarios</span>
                            </a>
                        </li>

                        <li class="pc-item">
                            <a href="../Perfil/" class="pc-link">
                                <span class="pc-micon">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-share-bold"></use>
                                    </svg>
                                </span>
                                <span class="pc-mtext">Perfiles</span>
                            </a>
                        </li>

                        <li class="pc-item pc-hasmenu">
                            <a href="#!" class="pc-link">
                                <span class="pc-micon">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-note-1"></use>
                                    </svg> 
                                </span>
                                <span class="pc-mtext">Reportes</span> <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="pc-submenu">
                                <li class="pc-item"><a class="pc-link" href="../demo/layout-vertical.html">Reporte 1</a></li>
                                <li class="pc-item"><a class="pc-link" href="../demo/layout-horizontal.html">Reporte 2</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>