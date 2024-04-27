<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <div class="form-message text-center"></div>
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
                                <div class="input-group-append">
                                    <a href="#" class="ml-2 cursor-pointer remove-btn"
                                        id="removeBtn_{{ $subKriteria->nilai_pembimbing_dosen_id }}"
                                        id="id{{ $subKriteria->nilai_pembimbing_dosen_id }}"
                                        data-subcategory-id="{{ $subKriteria->nilai_pembimbing_dosen_id }}"
                                        data-parent-id="{{ $subKriteria->parent_id }}">
                                        <i class="text-danger fa fa-trash"></i>
                                    </a>
                                </div>
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

<script>
    $(document).ready(function() {
        var baseUrl = "{{ url('category/nilai-pembimbing-dosen/') }}";
        var datasub = {!! json_encode($datasub) !!};
        console.log(datasub);
        $('.remove-btn').on('click', function(event) {
            event.preventDefault();
            var subcategoryId = $(this).data('subcategory-id');
            var parentId = $(this).data('parent-id');
            // console.log(subcategoryId, parentId);

            // Hapus item secara langsung
            $.ajax({
                // url: "{{ route('delete_sub_category_dosen_pembimbing', ['id' => $subKriteria->nilai_pembimbing_dosen_id]) }}",
                url: baseUrl + '/' + subcategoryId + '/subcategory/delete',
                //  url: subcategoryId + subcategoryIdValue,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: parentId,
                    subcategory_id: subcategoryId
                },
                success: function(response) {
                    if (response.stat) {
                        $('a[data-subcategory-id="' + subcategoryId + '"]')
                            .closest('.col-sm-8').remove();
                        $('.form-message').removeClass('text-danger').addClass(
                            'text-success').text(response.msg).show();
                        // Setelah menghapus, periksa apakah masih ada subkriteria yang tersisa
                        if ($('.colom-sub').find('.col-sm-8').length === 0) {
                            closeModal($modal, response);
                            location.reload();
                        }
                    } else {
                        $('.form-message').removeClass('text-success').addClass(
                            'text-danger').text(response.msg).show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    })
</script>
