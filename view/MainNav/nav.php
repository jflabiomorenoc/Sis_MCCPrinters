    <body data-pc-preset="preset-5" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light"><!-- [ Pre-loader ] start -->
        <div class="loader-bg">
            <div class="loader-track">
                <div class="loader-fill"></div>
            </div>
        </div>
        <nav class="pc-sidebar">
            <div class="navbar-wrapper">
                <div class="m-header">
                    <a href="../Dashboard/" class="b-brand text-primary">
                        <img src="../../assets/images/logo.jpg" class="img-fluid" alt="logo">
                    </a>
                </div>
                <div class="navbar-content">
                    <div class="card pc-user-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0"><img src="../../assets/images/user/<?php echo $_SESSION['foto_perfil'];?>" alt="user-image" class="user-avtar wid-45 rounded-circle"></div>
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
                                    <!-- <a href="#!"><i class="ti ti-user"></i> <span>Mi cuenta</span> </a> -->
                                    <!-- <a href="#!"><i class="ti ti-settings"></i> <span>Configuración</span> </a> -->
                                    <!-- <a href="#!"><i class="ti ti-lock"></i> <span>Bloquear</span> </a> -->
                                    <a href="../Logout/logout.php"><i class="ti ti-power"></i> <span>Salir</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="pc-navbar">
                        
                    </ul>
                </div>
            </div>
        </nav>