<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <div id="success-message" class="alert alert-success" style="display: none;"></div>
            <div class="form-group required row mb-2">
                <label class="col-sm-3 control-label col-form-label">Nama Kriteria Utama</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm"
                        value="{{ isset($data->name_kriteria_pembimbing_dosen) ? $data->name_kriteria_pembimbing_dosen : '' }}"
                        disabled />
                </div>
            </div>
            <div class="form-group required row mb-2">
                <label class="col-sm-3 control-label col-form-label">Bobot Kriteria</label>
                <div class="col-sm-8">
                    <input type="number" class="form-control form-control-sm"
                        value="{{ isset($data->bobot) ? $data->bobot : '' }}" disabled />
                </div>
            </div>
            @if ($data->subKriteria->isNotEmpty())
                <div class="form-group required row mb-2">
                    <label class="col-sm-3 control-label col-form-label">Sub Kriteria</label>
                    <div class="colom-sub" style="width: 605px;">
                        @foreach ($data->subKriteria as $subKriteria)
                            <div class="col-sm-8" style="margin-bottom: 8px;"> <!-- Atur spasi langsung di sini -->
                                <input type="text" class="form-control form-control-sm" style="width: 136%;"
                                    value="{{ $subKriteria->name_kriteria_pembimbing_dosen }}" disabled>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
        </div>
    </div>
</div>
