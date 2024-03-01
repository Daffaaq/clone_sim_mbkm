@php
    $periode = session()->get('periode_active');
    $role_name = $auth->getRoleName();
@endphp

<nav class="main-header navbar navbar-expand {{ $theme->navbar }}">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link font-weight-bold text-light" data-widget="pushmenu" href="#"><i
                    class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item">
            <div class=" d-inline-block">
                @if ($periode)
                    <button class="btn btn-light btn-sm mr-2">Periode : &nbsp; <i
                            class="fa fa-check-square text-primary"> </i>&nbsp;<span
                            class="text-primary font-weight-bold">
                            <strong>{{ $periode->periode_nama }}</strong></span></button>
                @else
                    <button class="btn btn-danger btn-sm mr-2"><i class="fa fa-ban"></i> Periode Magang belum di
                        set</button>
                @endif
            </div>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <div class="d-inline-block pt-1">
                <span class="text-bold text-light">{{ getServerDate() }} <span class="jclock"></span></span>
            </div>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle text-light" data-toggle="dropdown" aria-expanded="true">
                <img src="{{ $avatar }}" class="user-image img-circle elevation-1" alt="User Image">
                <span class="d-none d-md-inline font-weight-bold">{{ $role_name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="user-header bg-light">
                    <img src="{{ $avatar }}" class="img-circle elevation-2" alt="User Image">
                    <p>{{ $auth->username }}
                        <small> Anda login sebagai <strong>{{ $role_name }}</strong></small>
                    </p>
                </li>
                {{-- <li class="mb-2 text-center">
                    <div class="btn-group">
                        <a href="{{ url('theme/light') }}" class="btn btn-info text-light">Light Theme</a>
                        <a href="{{ url('theme/dark') }}" class="btn btn-dark text-light">Dark Theme</a>
                    </div>
                </li> --}}
                <li class="user-footer">
                    <a href="{{ url('setting/profile') }}" class="btn btn-{{ $theme->button }} text-light">Profile</a>
                    <a href="{{ url('logout') }}" class="btn btn-warning float-right text-secondary">Logout</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
