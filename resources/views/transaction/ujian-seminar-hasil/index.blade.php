@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <section class="col-lg-12">
                <div class="card card-outline card-{{ $theme->card_outline }}">
                    <div class="card-header">
                        <h3 class="card-title mt-1">
                            <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                            {!! $page->title !!}
                        </h3>
                    </div>
                    <div class="card-body p-0">

                        {{-- Form Anda bisa ditambahkan di sini --}}
                    </div>
            </section>
        </div>
    </div>
@endsection
