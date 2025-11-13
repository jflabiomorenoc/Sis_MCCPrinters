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
                        <!-- <li class="dropdown pc-h-item"><a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"><svg class="pc-icon">
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
                        </li> -->

                        <li class="dropdown pc-h-item header-user-profile"><a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false"><img src="../../assets/images/user/<?php echo $_SESSION['foto_perfil'];?>" alt="user-image" class="user-avtar"></a>
                            <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                                <div class="dropdown-header d-flex align-items-center justify-content-between">
                                    <h5 class="m-0">Perfil</h5>
                                </div>
                                <div class="dropdown-body">
                                    <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                                        <div class="d-flex mb-1">
                                            <div class="flex-shrink-0"><img src="../../assets/images/user/<?php echo $_SESSION['foto_perfil'];?>" alt="user-image" class="user-avtar wid-35"></div>
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
                                        <p class="text-span">Gestionar</p>
                                        <!-- <a href="#" class="dropdown-item">
                                            <span>
                                                <svg class="pc-icon text-muted me-2">
                                                    <use xlink:href="#custom-setting-outline"></use>
                                                </svg> 
                                                <span>Configuración</span> 
                                            </span>
                                        </a> -->
                                        <a id="cambiarPassword" class="dropdown-item">
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

        <div class="modal" id="modalCambiarPassword" role="dialog" role="dialog">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-lock me-2"></i>Cambiar Contraseña
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formCambiarPassword" autocomplete="off">
                        <div class="modal-body">
                            
                            <!-- Contraseña Actual -->
                            <div class="mb-3">
                                <label class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" 
                                        class="form-control" 
                                        id="password_actual" 
                                        name="password_actual"
                                        placeholder="Ingrese su contraseña actual"
                                        required
                                        autocomplete="new-password"
                                        onpaste="return false"
                                        oncopy="return false"
                                        oncut="return false">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_actual')">
                                        <i class="ti ti-eye" id="icon_password_actual"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Debe coincidir con su contraseña actual</small>
                            </div>
                            
                            <!-- Contraseña Nueva -->
                            <div class="mb-3">
                                <label class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" 
                                        class="form-control" 
                                        id="password_nueva" 
                                        name="password_nueva"
                                        placeholder="Ingrese su nueva contraseña"
                                        required
                                        autocomplete="new-password"
                                        onpaste="return false"
                                        oncopy="return false"
                                        oncut="return false"
                                        minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_nueva')">
                                        <i class="ti ti-eye" id="icon_password_nueva"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Mínimo 8 caracteres</small>
                                
                                <!-- Indicador de fortaleza -->
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" id="password_strength" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="password_strength_text" class="text-muted"></small>
                                </div>
                            </div>
                            
                            <!-- Confirmar Contraseña -->
                            <div class="mb-3">
                                <label class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" 
                                        class="form-control" 
                                        id="password_confirmar" 
                                        name="password_confirmar"
                                        placeholder="Confirme su nueva contraseña"
                                        required
                                        autocomplete="new-password"
                                        onpaste="return false"
                                        oncopy="return false"
                                        oncut="return false"
                                        minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmar')">
                                        <i class="ti ti-eye" id="icon_password_confirmar"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Debe coincidir con la nueva contraseña</small>
                            </div>
                            
                            <!-- Alertas -->
                            <div id="alert_password" class="alert alert-warning d-none" role="alert">
                                <i class="ti ti-alert-circle me-2"></i>
                                <span id="alert_password_text"></span>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnGuardarPassword">
                                Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>