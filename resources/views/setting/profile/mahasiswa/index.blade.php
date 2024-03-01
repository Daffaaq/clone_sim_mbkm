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
                                Profile Mahasiswa
                            </h3>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-message-profile text-center"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Jurusan</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-static mt-2">{{ $mhs->prodi->jurusan->jurusan_name }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Prodi</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-static mt-2">{{ $mhs->prodi->prodi_name }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Username/NIM</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-static mt-2">{{ $mhs->nim }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Nama</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="nama_mahasiswa"
                                                value="{{ $mhs->nama_mahasiswa }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="email_mahasiswa" value="{{ $mhs->email_mahasiswa }}">
                                            <small class="form-text text-muted">Masukkan alamat email. Untuk menggunakan
                                                SSO, masukkan alamat Email Polinema</small>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">HP</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="no_hp"
                                                value="{{ $mhs->no_hp }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">JK</label>
                                        <div class="col-sm-9 mt-2">
                                            <div class="icheck-{{ $theme->button }} d-inline mr-2">
                                                <input type="radio" id="radioAktif" name="jenis_kelamin" value="0"
                                                    <?php echo isset($mhs->jenis_kelamin) ? ($mhs->jenis_kelamin == '0' ? 'checked' : '') : ''; ?>>
                                                <label for="radioAktif">Perempuan </label>
                                            </div>
                                            <div class="icheck-warning d-inline">
                                                <input type="radio" id="radioNonAktif" name="jenis_kelamin" value="1"
                                                    <?php echo isset($mhs->jenis_kelamin) ? ($mhs->jenis_kelamin == '1' ? 'checked' : '') : 'checked'; ?>>
                                                <label for="radioNonAktif">Laki-laki</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {{-- <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Tahun Masuk</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm"
                                                name="mahasiswa_tahun" value="{{ $mhs->mahasiswa_tahun }}">
                                        </div>
                                    </div> --}}
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Kelas</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="kelas"
                                                value="{{ $mhs->kelas }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">Nama Orang Tua</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="nama_ortu"
                                                value="{{ $mhs->nama_ortu }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label class="col-sm-3 col-form-label">HP Orang Tua</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" name="hp_ortu"
                                                value="{{ $mhs->hp_ortu }}">
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

                },
                submitHandler: function(form) {
                    $('.form-message-profile').html('');
                    $(form).ajaxSubmit({
                        dataType: 'json',
                        success: function(data) {
                            setFormMessage('.form-message-profile', data);
                            if (data.stat) {
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            }
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
