@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <form method="post" action="{{ $page->url }}" role="form" class="form-horizontal" id="form-profile">
            @csrf
            @method('PUT')

            <div class="row">
                <section class="col-lg-12">
                    <div class="card card-outline card-{{ $theme->card_outline }}">
                        <div class="card-header">
                            <h3 class="card-title mt-1">
                                <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                                Profile Dosen
                            </h3>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-message-profile text-center"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">NIP</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="dosen_nip"
                                                value="{{ $dosen->dosen_nip }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">NIDN</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="dosen_nidn"
                                                value="{{ $dosen->dosen_nidn }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Nama</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="dosen_name"
                                                value="{{ $dosen->dosen_name }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="dosen_email"
                                                value="{{ $dosen->dosen_email }}">
                                            <small class="form-text text-muted">Masukkan alamat email. Untuk menggunakan
                                                SSO, masukkan alamat Email Polinema</small>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">HP</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="dosen_phone"
                                                value="{{ $dosen->dosen_phone }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">JK</label>
                                        <div class="col-sm-9 mt-2">
                                            <div class="icheck-{{ $theme->button }} d-inline mr-2">
                                                <input type="radio" id="radioAktif" name="dosen_gender" value="P"
                                                    <?php echo isset($dosen->dosen_gender) ? ($dosen->dosen_gender == 'P' ? 'checked' : '') : ''; ?>>
                                                <label for="radioAktif">Perempuan </label>
                                            </div>
                                            <div class="icheck-warning d-inline">
                                                <input type="radio" id="radioNonAktif" name="dosen_gender" value="L"
                                                    <?php echo isset($dosen->dosen_gender) ? ($dosen->dosen_gender == 'L' ? 'checked' : '') : 'checked'; ?>>
                                                <label for="radioNonAktif">Laki-laki</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Tahun Mengajar</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="dosen_tahun"
                                                value="{{ $dosen->dosen_tahun }}">
                                            <small class="form-text text-muted">Tahun pertama mengajar di Polinema</small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-3"></label>
                                        <div class="col-sm-9">
                                            <button type="submit" class="btn btn-{{ $theme->button }}">Simpan
                                                Profile</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </form>
    </div>
@endsection

@push('content-js')
    <script>
        $(document).ready(function() {


            $("#form-profile").validate({
                rules: {
                    dosen_nip: {
                        exactlength: 18
                    },
                    dosen_nidn: {
                        number: true,
                        exactlength: 10
                    },
                    dosen_name: {
                        required: true,
                        maxlength: 50
                    },
                    dosen_email: {
                        required: true,
                        email: true,
                        maxlength: 50
                    },
                    dosen_phone: {
                        required: true,
                        number: true,
                        minlength: 8,
                        maxlength: 15
                    },
                    dosen_gender: {
                        required: true
                    },
                    // dosen_tahun: {
                    //     required: true,
                    //     min: 1945,
                    //     max: {{ date('Y') }}
                    // },
                    // dosen_jenis: {
                    //     required: true
                    // },
                    // dosen_status: {
                    //     required: true
                    // },
                    // sinta_id: {
                    //     url: true,
                    //     maxlength: 255
                    // },
                    // scholar_id: {
                    //     url: true,
                    //     maxlength: 255
                    // },
                    // scopus_id: {
                    //     url: true,
                    //     maxlength: 255
                    // },
                    // researchgate_id: {
                    //     url: true,
                    //     maxlength: 255
                    // },
                    // orcid_id: {
                    //     url: true,
                    //     maxlength: 255
                    // },
                    // 'bidang_id[]': {
                    //     required: true
                    // },
                },
                submitHandler: function(form) {
                    $('.form-message-profile').html('');
                    $(form).ajaxSubmit({
                        dataType: 'json',
                        success: function(data) {
                            setFormMessage('.form-message-profile', data);
                        }
                    });
                },
                validClass: "valid-feedback",
                errorElement: "div",
                errorClass: 'invalid-feedback',
                errorPlacement: erp,
                highlight: hl,
                unhighlight: uhl,
                success: sc
            });
        });
    </script>
@endpush
