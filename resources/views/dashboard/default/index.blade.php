@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
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
