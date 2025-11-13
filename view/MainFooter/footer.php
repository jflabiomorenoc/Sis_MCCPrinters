        <footer class="pc-footer">
            <div class="footer-wrapper container-fluid">
                <div class="row">
                    <div class="col my-1">
                        <p class="m-0">© 2025 <a href="#" target="_blank">MainCode</a></p>
                    </div>
                    <!-- <div class="col-md-auto my-1">
                        <ul class="list-inline footer-link mb-0">
                            <li class="list-inline-item"><a href="../index.html">Home</a></li>
                            <li class="list-inline-item"><a href="https://phoenixcoded.gitbook.io/able-pro/" target="_blank">Documentation</a></li>
                            <li class="list-inline-item tf"><a href="https://phoenixcoded.authordesk.app/" target="_blank">Support</a></li>
                            <li class="list-inline-item ct"><a href="https://codedthemes.support-hub.io/" target="_blank">Support</a></li>
                        </ul>
                    </div> -->
                </div>
            </div>
        </footer>

        <!-- <div class="pct-c-btn"><a href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_pc_layout"><i class="ph-duotone ph-gear-six"></i></a></div> -->

        <div class="offcanvas border-0 pct-offcanvas offcanvas-end" tabindex="-1" id="offcanvas_pc_layout">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Settings</h5><button type="button" class="btn btn-icon btn-link-danger ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ti ti-x"></i></button>
            </div>
            <div class="pct-body customizer-body">
                <div class="offcanvas-body py-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="pc-dark">
                                <h6 class="mb-1">Theme Mode</h6>
                                <p class="text-muted text-sm">Choose light or dark mode or Auto</p>
                                <div class="row theme-color theme-layout">
                                    <div class="col-4">
                                        <div class="d-grid"><button class="preset-btn btn active" data-value="true" onclick="layout_change('light');" data-bs-toggle="tooltip" title="Light"><svg class="pc-icon text-warning">
                                                    <use xlink:href="#custom-sun-1"></use>
                                                </svg></button></div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-grid"><button class="preset-btn btn" data-value="false" onclick="layout_change('dark');" data-bs-toggle="tooltip" title="Dark"><svg class="pc-icon">
                                                    <use xlink:href="#custom-moon"></use>
                                                </svg></button></div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-grid"><button class="preset-btn btn" data-value="default" onclick="layout_change_default();" data-bs-toggle="tooltip" title="Automatically sets the theme based on user's operating system's color scheme."><span class="pc-lay-icon d-flex align-items-center justify-content-center"><i class="ph-duotone ph-cpu"></i></span></button></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <h6 class="mb-1">Theme Contrast</h6>
                            <p class="text-muted text-sm">Choose theme contrast</p>
                            <div class="row theme-contrast">
                                <div class="col-6">
                                    <div class="d-grid"><button class="preset-btn btn" data-value="true" onclick="layout_theme_contrast_change('true');" data-bs-toggle="tooltip" title="True"><svg class="pc-icon">
                                                <use xlink:href="#custom-mask"></use>
                                            </svg></button></div>
                                </div>
                                <div class="col-6">
                                    <div class="d-grid"><button class="preset-btn btn active" data-value="false" onclick="layout_theme_contrast_change('false');" data-bs-toggle="tooltip" title="False"><svg class="pc-icon">
                                                <use xlink:href="#custom-mask-1-outline"></use>
                                            </svg></button></div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <h6 class="mb-1">Custom Theme</h6>
                            <p class="text-muted text-sm">Choose your primary theme color</p>
                            <div class="theme-color preset-color"><a href="#!" data-bs-toggle="tooltip" title="Blue" class="active" data-value="preset-1"><i class="ti ti-checks"></i></a> <a href="#!" data-bs-toggle="tooltip" title="Indigo" data-value="preset-2"><i class="ti ti-checks"></i></a> <a href="#!" data-bs-toggle="tooltip" title="Purple" data-value="preset-3"><i class="ti ti-checks"></i></a> <a href="#!" data-bs-toggle="tooltip" title="Pink" data-value="preset-4"><i class="ti ti-checks"></i></a> <a href="#!" data-bs-toggle="tooltip" title="Red" data-value="preset-5"><i class="ti ti-checks"></i></a> <a href="#!" data-bs-toggle="tooltip" title="Orange" data-value="preset-6"><i class="ti ti-checks"></i></a> <a href="#!" data-bs-toggle="tooltip" title="Yellow" data-value="preset-7"><i class="ti ti-checks"></i></a> <a href="#!" data-bs-toggle="tooltip" title="Green" data-value="preset-8"><i class="ti ti-checks"></i></a> <a href="#!" data-bs-toggle="tooltip" title="Teal" data-value="preset-9"><i class="ti ti-checks"></i></a> <a href="#!" data-bs-toggle="tooltip" title="Cyan" data-value="preset-10"><i class="ti ti-checks"></i></a></div>
                        </li>
                        <li class="list-group-item">
                            <h6 class="mb-1">Theme layout</h6>
                            <p class="text-muted text-sm">Choose your layout</p>
                            <div class="theme-main-layout d-flex align-center gap-1 w-100"><a href="#!" data-bs-toggle="tooltip" title="Vertical" class="active" data-value="vertical"><img src="../../assets/images/customizer/caption-on.svg" alt="img" class="img-fluid"> </a><a href="#!" data-bs-toggle="tooltip" title="Horizontal" data-value="horizontal"><img src="../../assets/images/customizer/horizontal.svg" alt="img" class="img-fluid"> </a><a href="#!" data-bs-toggle="tooltip" title="Color Header" data-value="color-header"><img src="../../assets/images/customizer/color-header.svg" alt="img" class="img-fluid"> </a><a href="#!" data-bs-toggle="tooltip" title="Compact" data-value="compact"><img src="../../assets/images/customizer/compact.svg" alt="img" class="img-fluid"> </a><a href="#!" data-bs-toggle="tooltip" title="Tab" data-value="tab"><img src="../../assets/images/customizer/tab.svg" alt="img" class="img-fluid"></a></div>
                        </li>
                        <li class="list-group-item">
                            <h6 class="mb-1">Sidebar Caption</h6>
                            <p class="text-muted text-sm">Sidebar Caption Hide/Show</p>
                            <div class="row theme-color theme-nav-caption">
                                <div class="col-6">
                                    <div class="d-grid"><button class="preset-btn btn-img btn active" data-value="true" onclick="layout_caption_change('true');" data-bs-toggle="tooltip" title="Caption Show"><img src="../../assets/images/customizer/caption-on.svg" alt="img" class="img-fluid"></button></div>
                                </div>
                                <div class="col-6">
                                    <div class="d-grid"><button class="preset-btn btn-img btn" data-value="false" onclick="layout_caption_change('false');" data-bs-toggle="tooltip" title="Caption Hide"><img src="../../assets/images/customizer/caption-off.svg" alt="img" class="img-fluid"></button></div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="pc-rtl">
                                <h6 class="mb-1">Theme Layout</h6>
                                <p class="text-muted text-sm">LTR/RTL</p>
                                <div class="row theme-color theme-direction">
                                    <div class="col-6">
                                        <div class="d-grid"><button class="preset-btn btn-img btn active" data-value="false" onclick="layout_rtl_change('false');" data-bs-toggle="tooltip" title="LTR"><img src="../../assets/images/customizer/ltr.svg" alt="img" class="img-fluid"></button></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-grid"><button class="preset-btn btn-img btn" data-value="true" onclick="layout_rtl_change('true');" data-bs-toggle="tooltip" title="RTL"><img src="../../assets/images/customizer/rtl.svg" alt="img" class="img-fluid"></button></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item pc-box-width">
                            <div class="pc-container-width">
                                <h6 class="mb-1">Layout Width</h6>
                                <p class="text-muted text-sm">Choose Full or Container Layout</p>
                                <div class="row theme-color theme-container">
                                    <div class="col-6">
                                        <div class="d-grid"><button class="preset-btn btn-img btn active" data-value="false" onclick="change_box_container('false')" data-bs-toggle="tooltip" title="Full Width"><img src="../../assets/images/customizer/full.svg" alt="img" class="img-fluid"></button></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-grid"><button class="preset-btn btn-img btn" data-value="true" onclick="change_box_container('true')" data-bs-toggle="tooltip" title="Fixed Width"><img src="../../assets/images/customizer/fixed.svg" alt="img" class="img-fluid"></button></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-grid"><button class="btn btn-light-danger" id="layoutreset">Reset Layout</button></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- [Page Specific JS] start -->
        <script src="../../assets/js/plugins/popper.min.js"></script>
        <script src="../../assets/js/plugins/simplebar.min.js"></script>
        <script src="../../assets/js/plugins/bootstrap.min.js"></script>
        <script src="../../assets/js/plugins/i18next.min.js"></script>
        <script src="../../assets/js/plugins/i18nextHttpBackend.min.js"></script>
        <script src="../../assets/js/icon/custom-font.js"></script>
        <script src="../../assets/js/script.js"></script>
        <script src="../../assets/js/theme.js"></script>
        <script src="../../assets/js/plugins/feather.min.js"></script>

        <script src="../../assets/vendor/vendors.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

        <script src="../../assets/js/plugins/dataTables.min.js"></script>
        <script src="../../assets/js/plugins/dataTables.bootstrap5.min.js"></script>

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

        <!-- Select2 JS (después de jQuery) -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

        <!-- CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.dataTables.min.css">

        <script src="../../assets/js/plugins/prism.js"></script>

        <script src="../MainNav/nav.js"></script>

        <script>
            //Tema
            layout_change('light');
            //Contraste
            layout_theme_contrast_change('true');
            //Color
            change_box_container('false');
            //Estilo de menú
            layout_caption_change('false');
            //Ubicación del menú
            layout_rtl_change('false');
            //Color
            preset_change('preset-5');
            //Orientación del menú
            main_layout_change('vertical');
        </script>

        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
                }
            });

            function getMessage(pStrType, pSrtText){
                Toast.fire({
                    icon: pStrType,
                    title:  pSrtText,
                });
            }

            // Función para formatear fecha y hora con AM/PM
            function formatearFecha(fecha) {
                if (!fecha) return '-';
                
                // Separar fecha y hora
                const partes = fecha.split(' ');
                
                if (partes.length === 2) {
                    // Tiene fecha y hora
                    const fechaParte = partes[0].split('-');
                    const horaParte = partes[1].split(':');
                    
                    if (fechaParte.length === 3 && horaParte.length >= 2) {
                        const fechaFormateada = fechaParte[2] + '/' + fechaParte[1] + '/' + fechaParte[0];
                        
                        // Convertir a formato 12 horas con AM/PM
                        let horas = parseInt(horaParte[0]);
                        const minutos = horaParte[1];
                        const ampm = horas >= 12 ? 'PM' : 'AM';
                        horas = horas % 12;
                        horas = horas ? horas : 12; // El '0' debe ser '12'
                        
                        const horaFormateada = String(horas).padStart(2, '0') + ':' + minutos + ' ' + ampm;
                        return fechaFormateada + ' ' + horaFormateada;
                    }
                } else {
                    // Solo tiene fecha (sin hora)
                    const fechaParte = fecha.split('-');
                    if (fechaParte.length === 3) {
                        return fechaParte[2] + '/' + fechaParte[1] + '/' + fechaParte[0];
                    }
                }
                
                return fecha;
            }
        </script>

        <script>
            $(document).ready(function() {
                
                // Evento click en el botón del header
                $('#cambiarPassword').on('click', function(e) {
                    e.preventDefault();
                    abrirModalCambiarPassword();
                });
                
                // Validar fortaleza de contraseña en tiempo real
                $('#password_nueva').on('keyup', function() {
                    validarFortalezaPassword($(this).val());
                });
                
                // Validar coincidencia de contraseñas
                $('#password_confirmar').on('keyup', function() {
                    validarCoincidenciaPasswords();
                });
                
                // Submit del formulario
                $('#formCambiarPassword').on('submit', function(e) {
                    e.preventDefault();
                    cambiarPassword();
                });
            });

            // Abrir modal
            function abrirModalCambiarPassword() {
                $('#formCambiarPassword')[0].reset();
                $('#alert_password').addClass('d-none');
                $('#password_strength').css('width', '0%').removeClass('bg-danger bg-warning bg-info bg-success');
                $('#password_strength_text').text('');
                $('#modalCambiarPassword').modal('show');
            }

            // Toggle mostrar/ocultar contraseña
            function togglePassword(inputId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById('icon_' + inputId);
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('ti-eye');
                    icon.classList.add('ti-eye-off');
                } else {
                    input.type = 'password';
                    icon.classList.remove('ti-eye-off');
                    icon.classList.add('ti-eye');
                }
            }

            // Validar fortaleza de contraseña
            function validarFortalezaPassword(password) {
                let fuerza = 0;
                let texto = '';
                let colorClass = '';
                
                if (password.length >= 8) fuerza += 25;
                if (password.length >= 12) fuerza += 25;
                if (/[a-z]/.test(password)) fuerza += 12.5;
                if (/[A-Z]/.test(password)) fuerza += 12.5;
                if (/[0-9]/.test(password)) fuerza += 12.5;
                if (/[^a-zA-Z0-9]/.test(password)) fuerza += 12.5;
                
                if (fuerza <= 25) {
                    texto = 'Muy débil';
                    colorClass = 'bg-danger';
                } else if (fuerza <= 50) {
                    texto = 'Débil';
                    colorClass = 'bg-warning';
                } else if (fuerza <= 75) {
                    texto = 'Media';
                    colorClass = 'bg-info';
                } else {
                    texto = 'Fuerte';
                    colorClass = 'bg-success';
                }
                
                $('#password_strength')
                    .css('width', fuerza + '%')
                    .removeClass('bg-danger bg-warning bg-info bg-success')
                    .addClass(colorClass);
                
                $('#password_strength_text').text(texto);
                
                return fuerza;
            }

            // Validar coincidencia de contraseñas
            function validarCoincidenciaPasswords() {
                const nueva = $('#password_nueva').val();
                const confirmar = $('#password_confirmar').val();
                
                if (confirmar && nueva !== confirmar) {
                    $('#password_confirmar').addClass('is-invalid');
                    mostrarAlerta('Las contraseñas no coinciden', 'warning');
                    return false;
                } else {
                    $('#password_confirmar').removeClass('is-invalid');
                    ocultarAlerta();
                    return true;
                }
            }

            // Cambiar contraseña
            function cambiarPassword() {
                const passwordActual = $('#password_actual').val();
                const passwordNueva = $('#password_nueva').val();
                const passwordConfirmar = $('#password_confirmar').val();
                
                // Validaciones
                if (!passwordActual || !passwordNueva || !passwordConfirmar) {
                    getMessage("warning", "Todos los campos son obligatorios");
                    return;
                }
                
                if (passwordNueva.length < 8) {
                    getMessage("warning", "La nueva contraseña debe tener mínimo 8 caracteres");
                    return;
                }
                
                if (passwordNueva !== passwordConfirmar) {
                    getMessage("warning", "Las contraseñas nuevas no coinciden");
                    return;
                }
                
                if (passwordActual === passwordNueva) {
                    getMessage("warning", "La nueva contraseña debe ser diferente a la actual");
                    return;
                }
                
                // Deshabilitar botón
                $('#btnGuardarPassword').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Cambiando...');
                
                // Enviar formulario
                $.ajax({
                    url: '../../controller/usuario.php?op=cambiar_password',
                    type: 'POST',
                    data: {
                        password_actual: passwordActual,
                        password_nueva: passwordNueva
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == 1) {

                            getMessage("success", response.message || "Error desconocido");
                            $('#modalCambiarPassword').modal('hide');
                            $('#formCambiarPassword')[0].reset();
                        } else {
                            mostrarAlerta(response.message, 'danger');
                        }
                    },
                    error: function() {
                        mostrarAlerta('Error al conectar con el servidor', 'danger');
                    },
                    complete: function() {
                        $('#btnGuardarPassword').prop('disabled', false).html('<i class="ti ti-device-floppy me-2"></i>Cambiar Contraseña');
                    }
                });
            }

            // Mostrar alerta
            function mostrarAlerta(mensaje, tipo) {
                $('#alert_password')
                    .removeClass('d-none alert-warning alert-danger alert-success')
                    .addClass('alert-' + tipo);
                $('#alert_password_text').text(mensaje);
            }

            // Ocultar alerta
            function ocultarAlerta() {
                $('#alert_password').addClass('d-none');
            }
        </script>
    </body><!-- [Body] end -->

</html>