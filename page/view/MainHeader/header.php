        <header class="pc-header">
            <div class="header-wrapper"><!-- [Mobile Media Block] start -->
                <div class="me-auto pc-mob-drp">
                    <ul class="list-unstyled"><!-- ======= Menu collapse Icon ===== -->
                        <li class="pc-h-item pc-sidebar-collapse"><a href="#" class="pc-head-link ms-0" id="sidebar-hide"><i class="ti ti-menu-2"></i></a></li>
                        <li class="pc-h-item pc-sidebar-popup"><a href="#" class="pc-head-link ms-0" id="mobile-collapse"><i class="ti ti-menu-2"></i></a></li>
                    </ul>
                </div><!-- [Mobile Media Block end] -->
                <div class="ms-auto">
                    <ul class="list-unstyled">
                        <li class="dropdown pc-h-item"><a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"><svg class="pc-icon">
                                    <use xlink:href="#custom-sun-1"></use>
                                </svg></a>
                            <div class="dropdown-menu dropdown-menu-end pc-h-dropdown"><a href="#!" class="dropdown-item" onclick="layout_change('dark')"><svg class="pc-icon">
                                        <use xlink:href="#custom-moon"></use>
                                    </svg> <span>Dark</span> </a><a href="#!" class="dropdown-item" onclick="layout_change('light')"><svg class="pc-icon">
                                        <use xlink:href="#custom-sun-1"></use>
                                    </svg> <span>Light</span> </a><a href="#!" class="dropdown-item" onclick="layout_change_default()"><svg class="pc-icon">
                                        <use xlink:href="#custom-setting-2"></use>
                                    </svg> <span>Default</span></a></div>
                        </li>
                        <li class="dropdown pc-h-item">
                            <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-setting-2"></use>
                                </svg>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                                <a href="#" class="dropdown-item"><i class="ti ti-user"></i> <span>My Account</span> </a>
                                <a href="#" class="dropdown-item"><i class="ti ti-settings"></i> <span>Settings</span> </a>
                                <a href="#" class="dropdown-item"><i class="ti ti-headset"></i> <span>Support</span> </a>
                                <a href="#" class="dropdown-item"><i class="ti ti-lock"></i> <span>Lock Screen</span> </a>
                                <a href="#" class="dropdown-item"><i class="ti ti-power"></i> <span>Logout</span></a>
                            </div>
                        </li>

                        <li class="dropdown pc-h-item header-user-profile"><a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false"><img src="../../../assets/images/user/<?php echo $_SESSION['foto_perfil'];?>" alt="user-image" class="user-avtar"></a>
                            <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                                <div class="dropdown-header d-flex align-items-center justify-content-between">
                                    <h5 class="m-0">Perfil</h5>
                                </div>
                                <div class="dropdown-body">
                                    <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                                        <div class="d-flex mb-1">
                                            <div class="flex-shrink-0"><img src="../../../assets/images/user/<?php echo $_SESSION['foto_perfil'];?>" alt="user-image" class="user-avtar wid-35"></div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1"><?php echo $_SESSION["nombres"] . ' ' . $_SESSION["apellidos"]; ?></h6>
                                                <span><?php echo $_SESSION["desc_rol"]; ?></span>
                                            </div>
                                        </div>
                                        <hr class="border-secondary border-opacity-50">
                                        <!-- <div class="card">
                                            <div class="card-body py-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <h5 class="mb-0 d-inline-flex align-items-center"><svg class="pc-icon text-muted me-2">
                                                            <use xlink:href="#custom-notification-outline"></use>
                                                        </svg>Notification</h5>
                                                    <div class="form-check form-switch form-check-reverse m-0"><input class="form-check-input f-18" type="checkbox" role="switch"></div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <p class="text-span">Administrar</p>
                                        <!-- <a href="#" class="dropdown-item">
                                            <span>
                                                <svg class="pc-icon text-muted me-2">
                                                    <use xlink:href="#custom-setting-outline"></use>
                                                </svg> 
                                                <span>Configuración</span> 
                                            </span>
                                        </a> -->
                                        <a href="#" class="dropdown-item">
                                            <span>
                                                <svg class="pc-icon text-muted me-2">
                                                    <use xlink:href="#custom-share-bold"></use>
                                                </svg> 
                                                <span>Cambiar contraseña</span>
                                            </span>
                                        </a>
                                        <hr class="border-secondary border-opacity-50">
                                        <div class="d-grid mb-3"><a class="btn btn-primary" href="../Logout/logout.php"><svg class="pc-icon me-2">
                                                    <use xlink:href="#custom-logout-1-outline"></use>
                                                </svg>Salir</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </header>