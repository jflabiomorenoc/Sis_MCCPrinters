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
                            <div class="theme-main-layout d-flex align-center gap-1 w-100"><a href="#!" data-bs-toggle="tooltip" title="Vertical" class="active" data-value="vertical"><img src="../../../assets/images/customizer/caption-on.svg" alt="img" class="img-fluid"> </a><a href="#!" data-bs-toggle="tooltip" title="Horizontal" data-value="horizontal"><img src="../../../assets/images/customizer/horizontal.svg" alt="img" class="img-fluid"> </a><a href="#!" data-bs-toggle="tooltip" title="Color Header" data-value="color-header"><img src="../../../assets/images/customizer/color-header.svg" alt="img" class="img-fluid"> </a><a href="#!" data-bs-toggle="tooltip" title="Compact" data-value="compact"><img src="../../../assets/images/customizer/compact.svg" alt="img" class="img-fluid"> </a><a href="#!" data-bs-toggle="tooltip" title="Tab" data-value="tab"><img src="../../../assets/images/customizer/tab.svg" alt="img" class="img-fluid"></a></div>
                        </li>
                        <li class="list-group-item">
                            <h6 class="mb-1">Sidebar Caption</h6>
                            <p class="text-muted text-sm">Sidebar Caption Hide/Show</p>
                            <div class="row theme-color theme-nav-caption">
                                <div class="col-6">
                                    <div class="d-grid"><button class="preset-btn btn-img btn active" data-value="true" onclick="layout_caption_change('true');" data-bs-toggle="tooltip" title="Caption Show"><img src="../../../assets/images/customizer/caption-on.svg" alt="img" class="img-fluid"></button></div>
                                </div>
                                <div class="col-6">
                                    <div class="d-grid"><button class="preset-btn btn-img btn" data-value="false" onclick="layout_caption_change('false');" data-bs-toggle="tooltip" title="Caption Hide"><img src="../../../assets/images/customizer/caption-off.svg" alt="img" class="img-fluid"></button></div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="pc-rtl">
                                <h6 class="mb-1">Theme Layout</h6>
                                <p class="text-muted text-sm">LTR/RTL</p>
                                <div class="row theme-color theme-direction">
                                    <div class="col-6">
                                        <div class="d-grid"><button class="preset-btn btn-img btn active" data-value="false" onclick="layout_rtl_change('false');" data-bs-toggle="tooltip" title="LTR"><img src="../../../assets/images/customizer/ltr.svg" alt="img" class="img-fluid"></button></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-grid"><button class="preset-btn btn-img btn" data-value="true" onclick="layout_rtl_change('true');" data-bs-toggle="tooltip" title="RTL"><img src="../../../assets/images/customizer/rtl.svg" alt="img" class="img-fluid"></button></div>
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
                                        <div class="d-grid"><button class="preset-btn btn-img btn active" data-value="false" onclick="change_box_container('false')" data-bs-toggle="tooltip" title="Full Width"><img src="../../../assets/images/customizer/full.svg" alt="img" class="img-fluid"></button></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-grid"><button class="preset-btn btn-img btn" data-value="true" onclick="change_box_container('true')" data-bs-toggle="tooltip" title="Fixed Width"><img src="../../../assets/images/customizer/fixed.svg" alt="img" class="img-fluid"></button></div>
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
        <script src="../../../assets/js/plugins/popper.min.js"></script>
        <script src="../../../assets/js/plugins/simplebar.min.js"></script>
        <script src="../../../assets/js/plugins/bootstrap.min.js"></script>
        <script src="../../../assets/js/plugins/i18next.min.js"></script>
        <script src="../../../assets/js/plugins/i18nextHttpBackend.min.js"></script>
        <script src="../../../assets/js/icon/custom-font.js"></script>
        <script src="../../../assets/js/script.js"></script>
        <script src="../../../assets/js/theme.js"></script>
        <script src="../../../assets/js/plugins/feather.min.js"></script>

        <script src="../../../assets/vendor/vendors.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

        <script src="../../../assets/js/plugins/dataTables.min.js"></script>
        <script src="../../../assets/js/plugins/dataTables.bootstrap5.min.js"></script>

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

        <!-- Select2 JS (después de jQuery) -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

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
        </script>

    </body><!-- [Body] end -->

</html>