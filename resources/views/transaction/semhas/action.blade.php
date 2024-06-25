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
                    <label class="col-sm-3 control-label col-form-label">Prodi</label>
                    <div class="col-sm-9">
                        @if (auth()->user()->prodi_id)
                            <!-- Menampilkan nilai prodi_id dan prodi_name langsung jika tersedia -->
                            @php
                                $selectedProdi = $prodis->where('prodi_id', auth()->user()->prodi_id)->first();
                            @endphp
                            <input type="hidden" name="prodi_id" value="{{ $selectedProdi->prodi_id }}">
                            <input type="text" class="form-control" value="{{ $selectedProdi->prodi_name }}"
                                readonly>
                        @else
                            <!-- Menampilkan dropdown jika prodi_id tidak tersedia -->
                            <select id="prodi_id" name="prodi_id"
                                class="form-control form-control-sm select2_combobox">
                                <option disabled selected value="">Pilih opsi</option>
                                @foreach ($prodis as $prodi)
                                    {{-- <option value="{{ $prodi->prodi_id }}">
                                        {{ $prodi->prodi_code }} - {{ $prodi->prodi_name }}
                                    </option> --}}
                                    <option value="{{ $prodi->prodi_id }}"
                                        {{ isset($data) && $data->prodi_id == $prodi->prodi_id ? 'selected' : '' }}>
                                        {{ $prodi->prodi_code }} - {{ $prodi->prodi_name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Judul Seminar Hasil</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control form-control-sm" id="judul_semhas" name="judul_semhas"
                            value="{{ isset($data->judul_semhas) ? $data->judul_semhas : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Gelombang</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control form-control-sm" id="gelombang" name="gelombang"
                            min="0" value="{{ isset($data->gelombang) ? $data->gelombang : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Kuota Bimbingan</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control form-control-sm" id="kuota_bimbingan"
                            name="kuota_bimbingan" min="0"
                            value="{{ isset($data->kuota_bimbingan) ? $data->kuota_bimbingan : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Deadline Nilai</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="number" class="form-control form-control-sm" id="deadline_nilai"
                                name="deadline_nilai" min="0"
                                value="{{ isset($data->deadline_nilai) ? $data->deadline_nilai : '' }}" />
                            <label class="form-control-sm custom-deadline_nilai-label" for="deadline_nilai">Hari</label>
                        </div>
                    </div>
                </div>

                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Tanggal Awal Pendaftaran</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control form-control-sm" id="tanggal_mulai_pendaftaran"
                            name="tanggal_mulai_pendaftaran"
                            value="{{ isset($data->tanggal_mulai_pendaftaran) ? $data->tanggal_mulai_pendaftaran : '' }}" />
                    </div>
                </div>
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Tanggal Akhir Pendaftaran</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control form-control-sm" id="tanggal_akhir_pendaftaran"
                            name="tanggal_akhir_pendaftaran"
                            value="{{ isset($data->tanggal_akhir_pendaftaran) ? $data->tanggal_akhir_pendaftaran : '' }}" />
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
        $("#form-master").validate({
            rules: {
                judul_semhas: {
                    required: true,
                },
                prodi_id: {
                    required: true,
                },
                gelombang: {
                    required: true,
                },
                kuota_bimbingan: {
                    required: true,
                },
                deadline_nilai: {
                    required: true,
                },
                tanggal_mulai_pendaftaran: {
                    required: true,
                },
                tanggal_akhir_pendaftaran: {
                    required: true,
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
