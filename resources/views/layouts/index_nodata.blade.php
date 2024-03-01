@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-danger">
                    <div class="card-header">
                        <h3 class="card-title mt-1">
                            <i class="fas fa-angle-double-right text-md text-danger mr-1"></i>
                            {{$subject}}
                        </h3>
                    </div><!-- /.card-header -->
                    <div class="card-body p-0">
                        <div class="alert alert-danger mb-0 rounded-0">
                            <h5><i class="icon fas fa-ban"></i>{{ $title }}</h5>
                            {{ $message }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
