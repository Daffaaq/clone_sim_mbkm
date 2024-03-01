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
                                Profile Perusahaan
                            </h3>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-message-profile text-center"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" class="form-control form-control-sm" id="id" name="id"
                                        value="{{ isset($perusahaan->perusahaan_id) ? $perusahaan->perusahaan_id : '' }}" />
                                    <div class="form-group required row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Nama Perusahaan</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="nama_perusahaan"
                                                name="nama_perusahaan"
                                                value="{{ isset($perusahaan->nama_perusahaan) ? $perusahaan->nama_perusahaan : '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group required row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="email"
                                                name="email"
                                                value="{{ isset($perusahaan->email) ? $perusahaan->email : '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group required row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Kategori</label>
                                        <div class="col-sm-9">
                                            <select data-testid="partner-category" class="form-control form-control-sm"
                                                id="kategori" name="kategori"
                                                value="{{ isset($perusahaan->kategori) ? $perusahaan->kategori : '' }}">
                                                <option disabled selected value="">Pilih opsi yang sesuai</option>
                                                <option value="BUMN"
                                                    {{ isset($perusahaan->kategori) ? ($perusahaan->kategori == 'BUMN' ? 'selected' : '') : '' }}>
                                                    BUMN
                                                </option>
                                                <option value="Perusahaan Terbuka"
                                                    {{ isset($perusahaan->kategori) ? ($perusahaan->kategori == 'Perusahaan Terbuka' ? 'selected' : '') : '' }}>
                                                    Perusahaan Terbuka</option>
                                                <option value="UMKM"
                                                    {{ isset($perusahaan->kategori) ? ($perusahaan->kategori == 'UMKM' ? 'selected' : '') : '' }}>
                                                    UMKM
                                                </option>
                                                <option value="Perusahaan Multinasional"
                                                    {{ isset($perusahaan->kategori) ? ($perusahaan->kategori == 'Perusahaan Multinasional' ? 'selected' : '') : '' }}>
                                                    Perusahaan Multinasional</option>
                                                <option value="Yayasan / Non-profit"
                                                    {{ isset($perusahaan->kategori) ? ($perusahaan->kategori == 'Yayasan / Non-profit' ? 'selected' : '') : '' }}>
                                                    Yayasan / Non-profit</option>
                                                <option value="Perusahaan Tertutup/Private"
                                                    {{ isset($perusahaan->kategori) ? ($perusahaan->kategori == 'Perusahaan Tertutup/Private' ? 'selected' : '') : '' }}>
                                                    Perusahaan Tertutup/Private</option>
                                                <option value="Instansi Pemerintahan"
                                                    {{ isset($perusahaan->kategori) ? ($perusahaan->kategori == 'Instansi Pemerintahan' ? 'selected' : '') : '' }}>
                                                    Instansi Pemerintahan</option>
                                                <option value="Instansi Multilalteral / Internasional"
                                                    {{ isset($perusahaan->kategori) ? ($perusahaan->kategori == 'Instansi Multilalteral / Internasional' ? 'selected' : '') : '' }}>
                                                    Instansi Multilalteral / Internasional</option>
                                                <option value="Perguruan Tinggi atau Satuan Pendidikan lainnya"
                                                    {{ isset($perusahaan->kategori) ? ($perusahaan->kategori == 'Perguruan Tinggi atau Satuan Pendidikan lainnya' ? 'selected' : '') : '' }}>
                                                    Perguruan Tinggi atau Satuan Pendidikan lainnya</option>
                                                <option value="Lainnya"
                                                    {{ isset($perusahaan->kategori) ? ($perusahaan->kategori == 'Lainnya' ? 'selected' : '') : '' }}>
                                                    Lainnya</option>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="form-group required row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Tipe Industri</label>
                                        <div class="col-sm-9">
                                            <select data-testid="partner-category" class="form-control form-control-sm"
                                                id="tipe_industri" name="tipe_industri">
                                                <option disabled selected value="">Pilih opsi yang sesuai</option>
                                                <option value="Energi dan Pertambangan"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Energi dan Pertambangan' ? 'selected' : '' }}>
                                                    Energi dan Pertambangan</option>
                                                <option value="Basic Material (Industri Kimia, Kertas, Kayu, dll)"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Basic Material (Industri Kimia, Kertas, Kayu, dll)' ? 'selected' : '' }}>
                                                    Basic Material (Industri Kimia, Kertas, Kayu, dll)</option>
                                                <option value="Jasa Industrial"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Jasa Industrial' ? 'selected' : '' }}>
                                                    Jasa Industrial</option>
                                                <option value="Makanan dan Minuman"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Makanan dan Minuman' ? 'selected' : '' }}>
                                                    Makanan dan Minuman</option>
                                                <option value="Pertanian dan Perkebunan"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Pertanian dan Perkebunan' ? 'selected' : '' }}>
                                                    Pertanian dan Perkebunan</option>
                                                <option value="FMCG"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'FMCG' ? 'selected' : '' }}>
                                                    FMCG</option>
                                                <option value="Otomotif"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Otomotif' ? 'selected' : '' }}>
                                                    Otomotif</option>
                                                <option value="Ritel"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Ritel' ? 'selected' : '' }}>
                                                    Ritel</option>
                                                <option value="Pariwisata"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Pariwisata' ? 'selected' : '' }}>
                                                    Pariwisata</option>
                                                <option value="Media"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Media' ? 'selected' : '' }}>
                                                    Media</option>
                                                <option value="Edukasi"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Edukasi' ? 'selected' : '' }}>
                                                    Edukasi</option>
                                                <option value="Hiburan dan Entertainment"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Hiburan dan Entertainment' ? 'selected' : '' }}>
                                                    Hiburan dan Entertainment</option>
                                                <option value="Kesehatan"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Kesehatan' ? 'selected' : '' }}>
                                                    Kesehatan</option>
                                                <option value="Perbankan dan Jasa Keuangan"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Perbankan dan Jasa Keuangan' ? 'selected' : '' }}>
                                                    Perbankan dan Jasa Keuangan</option>
                                                <option value="Real Estate"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Real Estate' ? 'selected' : '' }}>
                                                    Real Estate</option>
                                                <option value="Teknologi Informasi"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Teknologi Informasi' ? 'selected' : '' }}>
                                                    Teknologi Informasi</option>
                                                <option value="Infrastruktur (Telekomunikasi, Konstruksi, Alat Berat, dll)"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Infrastruktur (Telekomunikasi, Konstruksi, Alat Berat, dll)' ? 'selected' : '' }}>
                                                    Infrastruktur (Telekomunikasi, Konstruksi, Alat Berat, dll)</option>
                                                <option value="Logistik dan Transportasi"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Logistik dan Transportasi' ? 'selected' : '' }}>
                                                    Logistik dan Transportasi</option>
                                                <option value="Lainnya"
                                                    {{ isset($perusahaan->tipe_industri) && $perusahaan->tipe_industri == 'Lainnya' ? 'selected' : '' }}>
                                                    Lainnya</option>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="form-group required row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Alamat</label>
                                        <div class="col-sm-9">
                                            <textarea type="text" class="form-control form-control-sm" id="alamat" name="alamat">{{ isset($perusahaan->alamat) ? $perusahaan->alamat : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group required row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Provinsi</label>
                                        <div class="col-sm-9">
                                            <select data-testid="partner-category" class="form-control form-control-sm"
                                                id="provinsi_id" name="provinsi_id">
                                                <option disabled selected value="">Pilih provinsi</option>
                                                @foreach ($provinsis as $provinsi)
                                                    <option value="{{ $provinsi->id }}"
                                                        {{ isset($perusahaan->provinsi_id) && $perusahaan->provinsi_id == $provinsi->id ? 'selected' : '' }}>
                                                        {{ $provinsi->nama_provinsi }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group required row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Kabupaten/Kota</label>
                                        <div class="col-sm-9">
                                            <select data-testid="partner-category" class="form-control form-control-sm"
                                                id="kota_id" name="kota_id">
                                                <option disabled selected value="">Pilih kabupaten/kota</option>
                                                @foreach ($kabupatens as $kabupaten)
                                                    <option value="{{ $kabupaten->id }}"
                                                        {{ isset($perusahaan->kota_id) && $perusahaan->kota_id == $kabupaten->id ? 'selected' : '' }}>
                                                        {{ $kabupaten->nama_kab_kota }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group required row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Profil Perusahaan</label>
                                        <div class="col-sm-9">
                                            <textarea type="text" class="form-control form-control-sm" id="profil_perusahaan" name="profil_perusahaan">{{ isset($perusahaan->profil_perusahaan) ? $perusahaan->profil_perusahaan : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group required row mb-2">
                                        <label class="col-sm-3 control-label col-form-label">Website</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control form-control-sm" id="website"
                                                name="website"
                                                value="{{ isset($perusahaan->website) ? $perusahaan->website : '' }}" />
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

            $('.select2_combobox').select2();

            $('#provinsi_id').change(function() {
                var provinsi_id = $(this).val();
                if (provinsi_id) {
                    $.ajax({
                        url: "{{ url('kota') }}?provinsi_id=" + provinsi_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('#kota_id').empty();
                            $('#kota_id').append(
                                '<option disabled selected value="">Pilih kabupaten/kota</option>'
                            );
                            $.each(data, function(key, value) {
                                $('#kota_id').append('<option value="' + value.id +
                                    '">' + value.nama_kab_kota +
                                    '</option>');
                            });
                        }
                    });
                } else {
                    $('#kota_id').empty();
                    $('#kota_id').append(
                        '<option disabled selected value="">Pilih kabupaten/kota</option>');
                }
            });


            $("#form-profile").validate({
                rules: {
                    dosen_nip: {
                        number: true,
                        exactlength: 18
                    },
                    dosen_nidn: {
                        required: true,
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
                    dosen_tahun: {
                        required: true,
                        min: 1945,
                        max: {{ date('Y') }}
                    },
                    dosen_jenis: {
                        required: true
                    },
                    dosen_status: {
                        required: true
                    },
                    sinta_id: {
                        url: true,
                        maxlength: 255
                    },
                    scholar_id: {
                        url: true,
                        maxlength: 255
                    },
                    scopus_id: {
                        url: true,
                        maxlength: 255
                    },
                    researchgate_id: {
                        url: true,
                        maxlength: 255
                    },
                    orcid_id: {
                        url: true,
                        maxlength: 255
                    },
                    'bidang_id[]': {
                        required: true
                    },
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
