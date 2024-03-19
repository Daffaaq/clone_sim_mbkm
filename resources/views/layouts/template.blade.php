<?php
$auth = Auth::user();

$avatar = $auth->avatar_url;
$avatar = $avatar ? asset($avatar) : asset('assets/dist/user/user.png');
?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <link rel="dns-prefetch" href="https://polinema.ac.id">
    <link rel="dns-prefetch" href="http://tugasakhir.jti.polinema.ac.id">
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="Moch Zawaruddin Abdullah">
    <link rel="shortcut icon" href="{{ asset('polinema.png') }}" type="image/x-icon">
    <title>{{ config('app.name', 'E-LMS') }}</title>

    @if (env('enableCDN', false))
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"
            integrity="sha512-L7MWcK7FNPcwNqnLdZq86lTHYLdQqZaz5YcAgE+5cnGmlw8JT03QB2+oxL100UeB6RlzZLUxCGSS4/++mNZdxw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.10.0/css/OverlayScrollbars.min.css"
            integrity="sha512-lDpZRQrCqWR9wWLUscziLzK0KN7nKfrADal7rClvNC6O4sp1f4dIE9xVOlL9cbIoIvwRXs23V9erdl4YmN7iTA=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css"
            integrity="sha512-8vq2g5nHE062j3xor4XxPeZiPjmRDh6wlufQlfC6pdQ/9urJkU07NM0tEREeymP++NczacJ/Q59ul+/K2eYvcg=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.20/css/dataTables.bootstrap4.min.css"
            integrity="sha512-4o2NtfcBGIT0SbOTpWLYovl07cIaliKIQpUXvEPvyOgBF/01xY1TXm5F1B+X48/zhhFLIw2oBTsE0rjcwEOwJQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.11/css/select2.min.css"
            integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css"
            integrity="sha512-rBi1cGvEdd3NmSAQhPWId5Nd6QxE8To4ADjM2a6n0BrqQdisZ/RPUlm0YycDzvNL1HHAh1nKZqI0kSbif+5upQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css"
            integrity="sha512-kq3FES+RuuGoBW3a9R2ELYKRywUEQv0wvPTItv3DSGqjpbNtGWVdvT8qwdKkqvPzT93jp8tSF4+oN4IeTEIlQA=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.1/css/adminlte.min.css"
            integrity="sha512-cs64S0n/SFBu8iV4R0zXbTbqIXlMjubOWL1Sy9Bz1ofXd0HsfDNHjCwkBKNpHpH/ehEdCqPT8FUqVP5ooV0RrA=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
    @else
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-bs4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/dist/css/bs-stepper.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/dist/css/custom.min.css') }}">


    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>

    @stack('content-css')
</head>

<body
    class="sidebar-mini layout-fixed {{ $theme->mode }} layout-navbar-fixed layout-footer control-sidebar-slide-open accent-primary text-sm">
    <div class="wrapper">
        @include('layouts.navbar')
        <aside class="main-sidebar {{ $theme->sidebar }} elevation-4">
            <a href="#" class="brand-link {{ $theme->sidebar_navbar }}">
                <img src="{{ asset('polinema.png') }}" alt="{{ config('app.name', 'ELN') }}"
                    class="brand-image img-circle navbar-brand-image">
                <span class="brand-text font-weight-bold text-light">{{ env('APP_ALIAS') }}</span>
            </a>
            <div class="sidebar">
                @include('layouts.sidebar')
            </div>
        </aside>
        <div class="content-wrapper">
            @include('layouts.breadcrumb')
            <section class="content px-3">
                @yield('content')
                <div id="ajax-modal" class="modal fade animate shake" tabindex="-1" role="dialog"
                    data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"
                    data-close-on-escape="true"></div>
                <div id="ajax-modal-confirm" class="modal fade animate shake" tabindex="-1" role="dialog"
                    data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"
                    data-close-on-escape="true"></div>
            </section>
        </div>
        @include('layouts.footer')
    </div>

    @if (env('enableCDN', false))
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"
            integrity="sha512-bnIvzh6FU75ZKxp0GXLH9bewza/OIw6dLVh9ICg0gogclmYGguQJWl8U30WpbsGTqbIiAwxTsbe76DErLq5EDQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"
            integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"
            integrity="sha512-TqmAh0/sSbwSuVBODEagAoiUIeGRo8u95a41zykGfq5iPkO9oie8IKCgx7yAr1bfiBjZeuapjLgMdp9UMpCVYQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="{{ asset('assets/dist/js/forNestedModal.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.1/moment.min.js"
            integrity="sha512-qpOiaWh/f0WAbnVhbZelP1PfDJOlvdbAa/qqT7mrnwAX9uRDMXETSwch+iW6VCDC9X4dsK5okjC9wDPLnblyeQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.10.0/js/OverlayScrollbars.min.js"
            integrity="sha512-b08uXNWAD0s2v76NMjTS1XF+h/KynBB+q9o3/EW8+o/JEPkDLJazeB27kEFf+B72+N5oNrFQJmdyRczwZ1c+5A=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.20/js/jquery.dataTables.min.js"
            integrity="sha512-hX6rgGqXX6Ajh6Y+bZ+P/0ZkUBl3fQMY6I1B51h5NDOu7XE1lVgdf2VqygjozLX8AufHvWAzOuC0WVMb4wJX4w=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.20/js/dataTables.bootstrap4.min.js"
            integrity="sha512-T970v+zvIZu3UugrSpRoyYt0K0VknTDg2G0/hH7ZmeNjMAfymSRoY+CajxepI0k6VMFBXxgsBhk4W2r7NFg6ag=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script src="{{ asset('assets/plugins/jquery-ui/jquery.blockUI.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.11/js/select2.full.min.js"
            integrity="sha512-mGIhaSqC7YiMi2it8OToTXgg0RRHCNFVtCQyW9fPYhPOlrcQgkaSBNw8HQ8FLQxjSuDFQBbeeToTj5iFVoLLYw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"
            integrity="sha512-0QDLUJ0ILnknsQdYYjG7v2j8wERkKufvjBNmng/EdR/s/SE7X8cQ9y0+wMzuQT0lfXQ/NhG+zhmHNOWTUS3kMA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/jquery-validation/localization/messages_id.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/jquery-validation/jquery.form.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/4.0.9/jquery.inputmask.bundle.min.js"
            integrity="sha512-bQtKD9WcPsrfspLlSyh9kE6QP+kkj0y9kV4DDH25ID0iJpqCug06o+fBeuPpvSgzfiQN6hCPgvlq1STssJmFfg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"
            integrity="sha512-mh+AjlD3nxImTUGisMpHXW03gE6F4WdQyvuFRkjecwuWLwD2yCijw4tKA3NsEFpA1C3neiKhGXPSIGSfCYPMlQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="{{ asset('assets/plugins/jquery-file-download/ajaxdownloader.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/store-js/store.everything.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.1/js/adminlte.min.js"
            integrity="sha512-A492om6jtW/jTQioO8fpDRHVRR5jjP2d9RvqFoaP/sRHBuORYREu42G/tRiu489qVA1QRhyqtbr53wJDS4sl6g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @else
        <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/dist/js/forNestedModal.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

        <script src="{{ asset('assets/plugins/jquery-ui/jquery.blockUI.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/jquery-validation/localization/messages_id.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/jquery-validation/jquery.form.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/jquery-file-download/ajaxdownloader.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/store-js/store.everything.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
        <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
        <script src="{{ asset('assets/dist/js/bs-stepper.min.js') }}"></script>
    @endif
    <script src="{{ asset('assets/plugins/jquery-jclock/jquery.jclock.min.js') }}"></script>

    <script src="{{ asset('assets/dist/js/custom.min.js') }}"></script>
    <script>
        enableBlockUI = {{ config('custom.enableBlockUI') ? 'true' : 'false' }};
        setActiveMenu('{{ $activeMenu->l1 }}', '{{ $activeMenu->l2 }}', '{{ $activeMenu->l3 }}');
        var dataMaster, dataDetail;
        $(function($) {
            $('.jclock').jclock();
        });
    </script>
    @stack('content-js')
</body>

</html>
