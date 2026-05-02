<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', 'Toko Kain Garment') - Integrated ERP</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="shortcut icon" href="{{ asset('assets/vendors/media/logos/favicon.ico') }}" />
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    
    <!-- Global Stylesheets Bundle -->
    <link href="{{ asset('assets/vendors/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendors/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    
    <!-- Custom Stylesheets -->
    @stack('styles')
    
    <script>
        var hostUrl = "{{ url('/') }}/";
    </script>
</head>

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            
            <!--begin::Header-->
            <div id="kt_app_header" class="app-header" data-kt-sticky="true" data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize" data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
                <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
                    <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
                        <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                            <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                        <a href="{{ url('/') }}" class="d-lg-none">
                            <img alt="Logo" src="{{ asset('assets/vendors/media/logos/default-small.svg') }}" class="h-30px" />
                        </a>
                    </div>
                    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
                        <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
                            <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
                                <div class="menu-item here show menu-here-bg menu-lg-down-accordion me-0 me-lg-2">
                                    <span class="menu-link">
                                        <span class="menu-title">Toko Kain Integrated System</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="app-navbar flex-shrink-0">
                            <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
                                <div class="cursor-pointer symbol symbol-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                    <img src="{{ asset('assets/vendors/media/avatars/300-3.jpg') }}" class="rounded-3" alt="user" />
                                </div>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <div class="menu-content d-flex align-items-center px-3">
                                            <div class="symbol symbol-50px me-5">
                                                <img alt="Logo" src="{{ asset('assets/vendors/media/avatars/300-3.jpg') }}" />
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold d-flex align-items-center fs-5">{{ auth()->user()->name ?? 'User ERP' }}
                                                <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">Admin</span></div>
                                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{ auth()->user()->email ?? 'user@erp.com' }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="separator my-2"></div>
                                    <div class="menu-item px-5">
                                        <a href="{{ url('profile') }}" class="menu-link px-5">My Profile</a>
                                    </div>
                                    <div class="menu-item px-5">
                                        <a href="#" class="menu-link px-5" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign Out</a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Header-->

            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                
                <!--begin::Sidebar-->
                <div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
                    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
                        <a href="{{ url('dashboard') }}">
                            <img alt="Logo" src="{{ asset('assets/vendors/media/logos/default-dark.svg') }}" class="h-25px app-sidebar-logo-default" />
                            <img alt="Logo" src="{{ asset('assets/vendors/media/logos/default-small.svg') }}" class="h-20px app-sidebar-logo-minimize" />
                        </a>
                        <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
                            <i class="ki-duotone ki-double-left fs-2 rotate-180"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
                        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
                            <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="kt_app_sidebar_menu" data-kt-menu="true">
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('dashboard') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-element-11 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        </span>
                                        <span class="menu-title">Dashboard</span>
                                    </a>
                                </div>
                                @hasanyrole('admin|user')
                                <div class="menu-item pt-5">
                                    <div class="menu-content">
                                        <span class="menu-heading fw-bold text-uppercase fs-7">Inventory</span>
                                    </div>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->is('inventory/purchase-requisition*') ? 'active' : '' }}" href="{{ url('inventory/purchase-requisition') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-notepad fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        </span>
                                        <span class="menu-title">Pengajuan Beli</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->is('inventory/goods-receipt*') ? 'active' : '' }}" href="{{ url('inventory/goods-receipt') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-delivery-2 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></i>
                                        </span>
                                        <span class="menu-title">Input Nota</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->is('inventory/stocks*') ? 'active' : '' }}" href="{{ url('inventory/stocks') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-chart-line-star fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </span>
                                        <span class="menu-title">Stok</span>
                                    </a>
                                </div>
                                <div class="menu-item pt-5">
                                    <div class="menu-content">
                                        <span class="menu-heading fw-bold text-uppercase fs-7">Master Data</span>
                                    </div>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->is('master/product*') ? 'active' : '' }}" href="{{ url('master/product') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-briefcase fs-2"><span class="path1"></span><span class="path2"></span></i>
                                        </span>
                                        <span class="menu-title">Produk</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->is('master/supplier*') ? 'active' : '' }}" href="{{ url('master/supplier') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-shop fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        </span>
                                        <span class="menu-title">Supplier</span>
                                    </a>
                                </div>
                                @endhasanyrole

                                @role('admin')
                                <div class="menu-item pt-5">
                                    <div class="menu-content">
                                        <span class="menu-heading fw-bold text-uppercase fs-7 text-danger">Super Admin</span>
                                    </div>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ url('admin/users') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-user-square fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </span>
                                        <span class="menu-title">User Akses</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->is('admin/api*') ? 'active' : '' }}" href="{{ url('admin/api') }}">
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-key fs-2"><span class="path1"></span><span class="path2"></span></i>
                                        </span>
                                        <span class="menu-title">API</span>
                                    </a>
                                </div>
                                @endrole
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Sidebar-->

                <!--begin::Main-->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        
                        <!--begin::Toolbar-->
                        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">@yield('title')</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ url('/') }}" class="text-muted text-hover-primary">Home</a>
                                        </li>
                                        @yield('breadcrumb')
                                    </ul>
                                </div>
                                <div class="d-flex align-items-center gap-2 gap-lg-3">
                                    @yield('toolbar_actions')
                                </div>
                            </div>
                        </div>
                        <!--end::Toolbar-->

                        <!--begin::Content-->
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                @yield('content')
                            </div>
                        </div>
                        <!--end::Content-->

                    </div>

                    <!--begin::Footer-->
                    <div id="kt_app_footer" class="app-footer">
                        <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
                            <div class="text-gray-900 order-2 order-md-1">
                                <span class="text-muted fw-semibold me-1">2026&copy;</span>
                                <a href="#" target="_blank" class="text-gray-800 text-hover-primary">Toko Kain Garment</a>
                            </div>
                            <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
                                <li class="menu-item"><a href="#" target="_blank" class="menu-link px-2">Support</a></li>
                            </ul>
                        </div>
                    </div>
                    <!--end::Footer-->

                </div>
                <!--end::Main-->

            </div>
            <!--end::Wrapper-->

        </div>
    </div>

    <!--begin::Scrolltop-->
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-duotone ki-arrow-up"><span class="path1"></span><span class="path2"></span></i>
    </div>
    <!--end::Scrolltop-->

    <!-- Global Javascript Bundle -->
    <script src="{{ asset('assets/vendors/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/scripts.bundle.js') }}"></script>
    
    <!-- Custom Javascript -->
    @stack('scripts')
    
    <script>
        // Global AJAX setup to handle CSRF tokens and 419 session expired errors
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 419) {
                    console.error("CSRF token mismatch. Reloading page...");
                    window.location.reload();
                }
            }
        });
    </script>
</body>
</html>
