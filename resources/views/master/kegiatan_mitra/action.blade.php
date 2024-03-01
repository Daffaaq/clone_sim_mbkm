<?php
// jika $data ada ISI-nya maka actionnya adalah edit, jika KOSONG : insert
$is_edit = isset($data);
?>

<form method="post" action="{{ $page->url }}" role="form" class="form-horizontal" id="form-master">
    @csrf
    {!! $is_edit ? method_field('PUT') : '' !!}
    <div id="modal-master" class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-message text-center"></div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Tipe Kegiatan</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm"
                            id="tipe_kegiatan_id" name="tipe_kegiatan_id"
                            value="{{ isset($data->tipe_kegiatan_id) ? $data->tipe_kegiatan_id : '' }}">
                            <option disabled selected value="">Pilih opsi</option>
                            @foreach ($tipes as $tipe)
                                <option value="{{ $tipe->tipe_kegiatan_id }}"
                                    {{ isset($data->tipe_kegiatan_id) ? ($data->tipe_kegiatan_id == $tipe->tipe_kegiatan_id ? 'selected' : '') : '' }}>
                                    {{ $tipe->nama_kegiatan }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="form-group row mb-2 d-none" id="jenis_magang_form">
                    <label class="col-sm-3 control-label col-form-label">Jenis Magang</label>
                    <div class="col-sm-9">
                        <select data-testid="partner-category" class="form-control form-control-sm" id="jenis_magang_id"
                            name="jenis_magang_id"
                            value="{{ isset($data->jenis_magang_id) ? $data->jenis_magang_id : '' }}">
                            <option disabled selected value="">Pilih opsi</option>
                            @foreach ($jenises as $jenis)
                                <option value="{{ $jenis->jenis_magang_id }}"
                                    {{ isset($data->jenis_magang_id) ? ($data->jenis_magang_id == $jenis->jenis_magang_id ? 'selected' : '') : '' }}>
                                    {{ $jenis->nama_magang }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>
                {{-- @if ($koordinator)
                    <div class="form-group required row mb-2">
                        <label class="col-sm-3 control-label col-form-label">Periode</label>
                        <div class="col-sm-9">
                            <select data-testid="partner-category" class="form-control form-control-sm" id="periode_id"
                                name="periode_id" value="{{ isset($data->periode_id) ? $data->periode_id : '' }}">
                                <option disabled selected value="">Pilih opsi</option>
                                @foreach ($periodes as $periode)
                                    <option value="{{ $periode->periode_id }}"
                                        {{ isset($data->periode_id) ? ($periode->periode_id == $data->periode_id ? 'selected' : '') : '' }}>
                                        {{ $periode->semester }} - {{ $periode->tahun_ajar }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                @endif --}}
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Kode Kegiatan</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="kode_kegiatan"
                            name="kode_kegiatan"
                            value="{{ isset($data->kode_kegiatan) ? $data->kode_kegiatan : 'diisi otomatis' }}"
                            readonly />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Prodi</label>
                    <div class="col-sm-9 d-flex flex-column text-left pr-0">
                        @foreach ($prodis as $prodi)
                            @php
                                if (isset($data->prodi_id)) {
                                    $prodi_value = json_decode($data->prodi_id);
                                } else {
                                    $prodi_value = [];
                                }
                            @endphp
                            <div class="icheck-success d-inline">
                                <input name="prodi_arr[]" value={{ $prodi->prodi_id }} type="checkbox"
                                    onchange="updateCheck(this,'.r_act')" class="r_act"
                                    id="prodi_{{ $prodi->prodi_id }}"
                                    @if (isset($data->prodi_id)) @if (in_array($prodi->prodi_id, $prodi_value))
                                                checked @endif
                                    @endif
                                />
                                <label for="prodi_{{ $prodi->prodi_id }}" class="mr-2">
                                    {{ $prodi->prodi_code }} - {{ $prodi->prodi_name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Posisi Lowongan</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="posisi_lowongan"
                            name="posisi_lowongan"
                            value="{{ isset($data->posisi_lowongan) ? $data->posisi_lowongan : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Kuota</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control form-control-sm" id="kuota" name="kuota"
                            value="{{ isset($data->kuota) ? $data->kuota : '0' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Deskripsi</label>
                    <div class="col-sm-9">
                        <textarea type="text" class="form-control form-control-sm" id="deskripsi" name="deskripsi">{{ isset($data->deskripsi) ? $data->deskripsi : '' }}</textarea>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Kualifikasi</label>
                    <div class="col-sm-9">
                        <textarea type="text" class="form-control form-control-sm" id="kualifikasi" name="kualifikasi">{{ isset($data->kualifikasi) ? $data->kualifikasi : '' }}</textarea>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Fasilitas/Benefit</label>
                    <div class="col-sm-9">
                        <textarea type="text" class="form-control form-control-sm" id="fasilitas" name="fasilitas">{{ isset($data->fasilitas) ? $data->fasilitas : '' }}</textarea>
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Mulai Kegiatan</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control form-control-sm" id="mulai_kegiatan"
                            name="mulai_kegiatan"
                            value="{{ isset($data->mulai_kegiatan) ? $data->mulai_kegiatan : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Akhir Kegiatan</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control form-control-sm" id="akhir_kegiatan"
                            name="akhir_kegiatan"
                            value="{{ isset($data->akhir_kegiatan) ? $data->akhir_kegiatan : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Batas Pendaftaran</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control form-control-sm" id="batas_pendaftaran"
                            name="batas_pendaftaran"
                            value="{{ isset($data->batas_pendaftaran) ? $data->batas_pendaftaran : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Contact Person (Nama-No HP)</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="contact_person"
                            name="contact_person"
                            value="{{ isset($data->contact_person) ? $data->contact_person : '' }}" />
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Flyer</label>
                    <div class="col-sm-9">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input form-control-sm" id="file"
                                name="file" />
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                        <div class="mt-2">
                            @if (isset($data->flyer))
                                <a href="{{ asset('assets/' . $data->flyer) }}" target="_blank">
                                    <img src="{{ asset('assets/flyer/' . $data->flyer) }}" alt="flyer"
                                        width="100px">
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        unblockUI();

        $('#tipe_kegiatan_id').on('change', function() {
            var value = $(this).val()
            if (value == 1) {
                $('#jenis_magang_form').removeClass('d-none');
            } else {
                $('#jenis_magang_form').addClass('d-none');
            }
        })

        $("#form-master").validate({
            rules: {
                tipe_kegiatan_id: {
                    required: true,
                },
                periode_id: {
                    required: true
                },
                posisi_lowongan: {
                    required: true
                },
                deskripsi: {
                    required: true
                },
                kuota: {
                    required: true,
                    number: true,
                    //must not be zero
                    min: 1
                },
                mulai_kegiatan: {
                    required: true
                },
                akhir_kegiatan: {
                    required: true
                }
            },
            submitHandler: function(form) {
                $('.form-message').html('');
                blockUI(form);
                $(form).ajaxSubmit({
                    dataType: 'json',
                    success: function(data) {
                        unblockUI(form);
                        setFormMessage('.form-message', data);
                        if (data.stat) {
                            resetForm('#form-master');
                            dataMaster.draw(false);
                        }
                        closeModal($modal, data);
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
