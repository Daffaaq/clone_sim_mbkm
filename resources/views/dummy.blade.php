<?php
$auth = Auth::user();

$avatar = asset('assets/dist/user/user.png');
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
    @endif
    <link rel="stylesheet" href="{{ asset('assets/dist/css/custom.min.css') }}">

    @stack('content-css')
</head>

<body
    class="sidebar-mini layout-fixed {{ $theme->mode }} layout-navbar-fixed layout-footer control-sidebar-slide-open accent-primary text-sm">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-dark">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold text-light" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item">
                    <div class=" d-inline-block">
                                            <button class="btn btn-light btn-sm mr-2" fdprocessedid="k62td6">Periode : &nbsp; <i class="fa fa-check-square text-primary"> </i>&nbsp;<span class="text-primary font-weight-bold">
                                    <strong>Genap 2023/2024</strong></span></button>
                                    </div>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <div class="d-inline-block pt-1">
                        <span class="text-bold text-light">Sabtu, 10 Februari 2024 <span class="jclock" currenttime="1707576922119">21:55:22</span></span>
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle text-light" data-toggle="dropdown" aria-expanded="true">
                        <img src="http://127.0.0.1:8002/assets/dist/user/user.png" class="user-image img-circle elevation-1" alt="User Image">
                        <span class="d-none d-md-inline font-weight-bold">Koordinator MBKM</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <li class="user-header bg-light">
                            <img src="http://127.0.0.1:8002/assets/dist/user/user.png" class="img-circle elevation-2" alt="User Image">
                            <p>koordinator
                                <small> Anda login sebagai <strong>Koordinator MBKM</strong></small>
                            </p>
                        </li>

                        <li class="user-footer">
                            <a href="http://127.0.0.1:8002/setting/profile" class="btn btn-primary text-light">Profile</a>
                            <a href="http://127.0.0.1:8002/logout" class="btn btn-warning float-right text-secondary">Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <aside class="main-sidebar {{ $theme->sidebar }} elevation-4">
            <a href="#" class="brand-link {{ $theme->sidebar_navbar }}">
                <img src="{{ asset('polinema.png') }}" alt="{{ config('app.name', 'ELN') }}"
                    class="brand-image img-circle navbar-brand-image">
                <span class="brand-text font-weight-bold text-light">{{ env('APP_ALIAS') }}</span>
            </a>
            <div
                class="sidebar os-host os-theme-light os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-scrollbar-vertical-hidden os-host-transition">
                <div class="os-resize-observer-host">
                    <div class="os-resize-observer observed" style="left: 0px; right: auto;"></div>
                </div>
                <div class="os-size-auto-observer" style="height: calc(100% + 1px); float: left;">
                    <div class="os-resize-observer observed"></div>
                </div>
                <div class="os-content-glue" style="margin: 0px -8px; width: 249px; height: 618px;"></div>
                <div class="os-padding">
                    <div class="os-viewport os-viewport-native-scrollbars-invisible" style="right: 0px; bottom: 0px;">
                        <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">
                            <div class="user-panel mt-1 pb-1 mb-1 d-flex">
                                <div class="info">
                                    <a href="http://127.0.0.1:8002" class="d-block text-primary">Koordinator MBKM</a>
                                </div>
                            </div>
                            <nav>
                                <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-compact"
                                    data-widget="treeview" role="menu" data-accordion="false">
                                    <li class="nav-item"><a href="http://127.0.0.1:8002"
                                            class="nav-link dashboard l1"><i
                                                class="nav-icon fas fas fa-tachometer-alt "></i>
                                            <p>Dashboard</p>
                                        </a></li>
                                    <li class="nav-item"><a href="http://127.0.0.1:8002/mitra"
                                            class="nav-link mitra l1"><i class="nav-icon fas fas fa-building "></i>
                                            <p>Mitra</p>
                                        </a></li>
                                    <li class="nav-item has-treeview menu-open"><a href="#"
                                            class="nav-link master l1 active"><i class="nav-icon fas fas fa-th"></i>
                                            <p>Data Master<i class="fas fa-angle-left right"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item"><a href="http://127.0.0.1:8002/master/jurusan"
                                                    class="nav-link master-jurusan l2 active"><i
                                                        class="nav-icon fas fas fa-minus text-xs text-xs"></i>
                                                    <p>Jurusan</p>
                                                </a></li>
                                            <li class="nav-item"><a href="http://127.0.0.1:8002/master/prodi"
                                                    class="nav-link master-prodi l2"><i
                                                        class="nav-icon fas fas fa-minus text-xs text-xs"></i>
                                                    <p>Program Studi</p>
                                                </a></li>
                                            <li class="nav-item"><a href="http://127.0.0.1:8002/master/program"
                                                    class="nav-link master-program l2"><i
                                                        class="nav-icon fas fas fa-minus text-xs text-xs"></i>
                                                    <p>Program</p>
                                                </a></li>
                                            <li class="nav-item"><a href="http://127.0.0.1:8002/master/kegiatan"
                                                    class="nav-link master-kegiatan l2"><i
                                                        class="nav-icon fas fas fa-minus text-xs text-xs"></i>
                                                    <p>Kegiatan</p>
                                                </a></li>
                                            <li class="nav-item"><a href="http://127.0.0.1:8002/master/periode"
                                                    class="nav-link master-periode l2"><i
                                                        class="nav-icon fas fas fa-minus text-xs text-xs"></i>
                                                    <p>Periode</p>
                                                </a></li>
                                            <li class="nav-item"><a href="http://127.0.0.1:8002/master/mahasiswa"
                                                    class="nav-link master-mahasiswa l2"><i
                                                        class="nav-icon fas fas fa-minus text-xs text-xs"></i>
                                                    <p>Mahasiswa</p>
                                                </a></li>
                                        </ul>
                                    </li>
                                    <li class="nav-item has-treeview"><a href="#"
                                            class="nav-link transaction l1"><i class="nav-icon fas fas fa-edit"></i>
                                            <p>Transaksi<i class="fas fa-angle-left right"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item"><a href="http://127.0.0.1:8002/transaksi/magang"
                                                    class="nav-link transaksi-magang l2"><i
                                                        class="nav-icon fas fas fa-minus text-xs text-xs"></i>
                                                    <p>Magang</p>
                                                </a></li>
                                        </ul>
                                    </li>
                                    <li class="nav-item"><a href="#" class="nav-link report l1"><i
                                                class="nav-icon fas fas fa-file-invoice "></i>
                                            <p>Laporan</p>
                                        </a></li>
                                    <li class="nav-item has-treeview"><a href="#"
                                            class="nav-link setting l1"><i class="nav-icon fas fas fa-cogs"></i>
                                            <p>Setting<i class="fas fa-angle-left right"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item"><a href="http://127.0.0.1:8002/setting/account"
                                                    class="nav-link setting-accont l2"><i
                                                        class="nav-icon fas fas fa-minus text-xs text-xs"></i>
                                                    <p>Account</p>
                                                </a></li>
                                        </ul>
                                    </li>
                                    <li class="nav-item"><a href="http://127.0.0.1:8002/berita"
                                            class="nav-link berita-terkini l1"><i
                                                class="nav-icon fas fas fa-newspaper text-xs "></i>
                                            <p>Berita Terkini</p>
                                        </a></li>

                                    <li class="nav-item mt-1 border-top pt-1">
                                        <a href="http://127.0.0.1:8002/logout" class="nav-link l1">
                                            <i class="nav-icon fas fa-sign-out-alt "></i>
                                            <p>Logout</p>
                                        </a>
                                    </li>

                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
                    <div class="os-scrollbar-track">
                        <div class="os-scrollbar-handle" style="width: 100%; transform: translate(0px, 0px);"></div>
                    </div>
                </div>
                <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-unusable os-scrollbar-auto-hidden">
                    <div class="os-scrollbar-track">
                        <div class="os-scrollbar-handle" style="height: 100%; transform: translate(0px, 0px);"></div>
                    </div>
                </div>
                <div class="os-scrollbar-corner"></div>
            </div>
        </aside>
        <div class="content-wrapper">
            @include('layouts.breadcrumb')
            <section class="content px-3">
                <div class="container-fluid">
                    <div class="row">
                        <section class="col-lg-12">
                            <div class="card card-outline card-{{ $theme->card_outline }}">
                                <div class="card-header">
                                    <h3 class="card-title mt-1">
                                        <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                                        {!! $page->title !!}
                                    </h3>
                                    <div class="card-tools">
                                        @if($allowAccess->create)
                                            <button type="button" data-block="body" class="btn btn-sm btn-{{ $theme->button }} mt-1 ajax_modal" data-url="{{ $page->url }}/create"><i class="fas fa-plus"></i> Tambah</button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-hover table-full-width" id="table_master">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode</th>
                                                <th>Jurusan</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
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
     <script>
        $(document).ready(function() {

            $('.filter_combobox').select2();

            var v = 0;
            dataMaster = $('#table_master').DataTable({
                "bServerSide": true,
                "bAutoWidth": false,
                "ajax": {
                    "url": "{{ $page->url }}/list",
                    "dataType": "json",
                    "type": "POST"
                },
                "aoColumns": [{
                        "mData": "no",
                        "sClass": "text-center",
                        "sWidth": "5%",
                        "bSortable": false,
                        "bSearchable": false
                    },
                    {
                        "mData": "code",
                        "sClass": "",
                        "sWidth": "20%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "name",
                        "sClass": "",
                        "sWidth": "65%",
                        "bSortable": true,
                        "bSearchable": true
                    },
                    {
                        "mData": "id",
                        "sClass": "text-center pr-2",
                        "sWidth": "10%",
                        "bSortable": false,
                        "bSearchable": false,
                        "mRender": function(data, type, row, meta) {
                            return  ''
                                    @if(!$allowAccess->update) + `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/edit" class="ajax_modal btn btn-xs btn-warning tooltips text-secondary" data-placement="left" data-original-title="Edit Data" ><i class="fa fa-edit"></i></a> ` @endif
                                    @if(!$allowAccess->delete) + `<a href="#" data-block="body" data-url="{{ $page->url }}/${data}/delete" class="ajax_modal btn btn-xs btn-danger tooltips text-light" data-placement="left" data-original-title="Hapus Data" ><i class="fa fa-trash"></i></a> ` @endif
                            ;
                        }
                    }
                ],
                "fnDrawCallback": function ( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    $( 'a', this.fnGetNodes() ).tooltip();
                }
            });

            $('.dataTables_filter input').unbind().bind('keyup', function(e) {
                if (e.keyCode == 13) {
                    dataMaster.search($(this).val()).draw();
                }
            });
        });

    </script>
</body>

</html>
