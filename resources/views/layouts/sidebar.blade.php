@php
    $group_id = Auth::user()->group_id;
@endphp

@inject('menuProvider', 'App\Http\Controllers\Auth\LoginController')

<div class="user-panel mt-1 pb-1 mb-1 d-flex">
    <div class="info">
        <a href="{{ url('/') }}" class="d-block text-primary">{{ $auth->name }}</a>
    </div>
</div>
<nav>
    <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-compact" data-widget="treeview" role="menu"
        data-accordion="false">
        {!! session()->get('userMenu') !!}
        {{-- {!! $menuProvider->getMenu($group_id) !!} --}}
        <li class="nav-item mt-1 border-top pt-1">
            <a href="{{ url('logout') }}" class="nav-link l1">
                <i class="nav-icon fas fa-sign-out-alt "></i>
                <p>Logout</p>
            </a>
        </li>

    </ul>
</nav>
