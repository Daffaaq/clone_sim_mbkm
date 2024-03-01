@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if ($perusahaan->status == 0)
                    <div class="card bg-warning">
                        <div class="card-body">
                            <b>
                                Lengkapi Data Perusaahan Anda di Menu Profil! klik <a
                                    href="{{ route('perusahaan.profile') }}">disini</a>
                            </b>
                        </div>
                    </div>
                @endif
                <div class="card card-outline card-{{ $theme->card_outline }}">
                    <div class="card-body">
                        <h4>Selamat Datang, {{ auth()->user()->name }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('content-js')
@endpush
