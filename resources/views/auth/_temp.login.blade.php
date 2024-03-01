<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    {{--<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dist/css/custom.css') }}">--}}

    @stack('content-css')

    <link href="{{ asset('css/compact.css') }}" rel="stylesheet">
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>Halaman Login</b></a>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <div class="login-message text-center"></div>
                <form action="{{ route('login') }}" method="post" id="login-form">
                    @csrf
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        <input id="username" name="username" type="text" class="form-control" placeholder="Username" require  autocomplete="off"/>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Password" require />
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-validation/localization/messages_id.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.form.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-ui/jquery.blockUI.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/store-js/store.everything.min.js') }}"></script>--}}

    <script src="{{ asset('js/compact.js') }}"></script>

    <script>
        enableBlockUI = {{ (config('custom.enableBlockUI'))? 'true' : 'false' }};
    </script>

    {{--<script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('assets/dist/js/custom.js') }}"></script>--}}

    <script>
        $(document).ready(function() {
            $("#login-form").validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 4,
                        maxlength: 20
                    },
                    password: {
                        required: true,
                        minlength: 4,
                        maxlength: 20
                    }
                },
                submitHandler: function(form) {
                    $('.login-message').html('');
                    blockUI('body');
                    $(form).ajaxSubmit({
                        dataType:  'json',
					    success: function(data){
                            setFormMessage('.login-message', data);
                            if(data.stat){
                                /*let op1 = '<option value="">- Pilih Kecamatan -</option>';
                                $.each(data.kec, function (i, t) {
                                    op1 += '<option value="' + t.kecamatan_id + '">' + t.kecamatan + '</option>';
                                    let op2 = '<option value="">- Pilih Kelurahan -</option>';
                                    $.each(data.kel, function (j, u) {
                                        op2 += (u.kecamatan_id == t.kecamatan_id)? '<option value="' + u.kelurahan_id + '">' + u.kelurahan + '</option>' : '';
                                    });
                                    setStore(t.kecamatan_id, op2);
                                });
                                setStore('kecamatan', op1);*/

                                setTimeout(() => {
                                    window.location = data.url;
                                }, 1500);
                            }else{
                                unblockUI('body');
								refreshToken(data);
                            }
                        }
                    });
                },
                validClass: "valid--feedback",
                errorElement: "div", 
                errorClass: 'invalid-feedback',
                errorPlacement: erp,
                highlight: hl,
                unhighlight: uhl,
                success: sc
            });
            store.clearAll();
        });
    </script>
</body>

</html>
