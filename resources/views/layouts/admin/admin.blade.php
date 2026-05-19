<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Nowa Admin')</title>
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="keywords" content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">
    
    @includeIf('partials.admin.assets')
    @stack('head')

</head>

<body>

    <!-- Start Switcher (kept from template) -->
    @includeWhen(View::exists('partials.admin.switcher'), 'partials.admin.switcher')
    <!-- End Switcher -->

    <!-- Loader -->
    <div id="loader" >
        <img src="{{ asset('assets/images/media/loader.svg') }}" alt="">
    </div>
    <!-- Loader -->

    <div class="page">
         <!-- app-header -->
         @includeWhen(View::exists('partials.admin.header'), 'partials.admin.header')
        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        @includeWhen(View::exists('partials.admin.sidebar'), 'partials.admin.sidebar')
        <!-- End::app-sidebar -->

        <!-- main-content -->
        <div class="main-content app-content">

            <!-- container -->
            <div class="main-container container-fluid">

                @yield('content')

            </div>
            <!-- Container closed -->
        </div>
        <!-- main-content closed -->

        <!-- Footer Start -->
        <footer class="footer mt-auto py-3 bg-white text-center">
            <div class="container">
                <span> Copyright © <span id="year"></span> <a
                        href="javascript:void(0);" class="text-primary">Nowa</a>.
                    Designed with <span class="bi bi-heart-fill text-danger"></span> by <a href="javascript:void(0);">
                        <span class="fw-semibold text-decoration-underline">Spruko</span>
                    </a> All
                    rights
                    reserved
                </span>
            </div>
        </footer>
        <!-- Footer End -->

        <!-- Rightsidebar placeholder -->
        @includeWhen(View::exists('partials.admin.rightsidebar'), 'partials.admin.rightsidebar')


    </div>

    
    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->

    @includeIf('partials.admin.scripts')
    @stack('scripts')

</body>

</html>
