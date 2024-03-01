<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <table class="table table-sm mb-0">
                <tr><th class="w-25 text-right">Kode Jenis</th><th class="w-1">:</th><td class="w-74">{{ $data->customer_type_code }}</td></tr>
                <tr><th class="w-25 text-right">Nama Jenis</th><th class="w-1">:</th><td class="w-74">{{ $data->customer_type_name }}</td></tr>
                <tr><th class="w-25 text-right">Biaya Pendaftaran</th><th class="w-1">:</th><td class="w-74">{{ formatMoney($data->biaya_pendaftaran) }}</td></tr>
                <tr><th class="w-25 text-right">Biaya Beban</th><th class="w-1">:</th><td class="w-74">Rp {{ formatMoney($data->beban) }} <small class="text-primary">Biaya beban penggunaan layanan. Dibayar tiap tagihan per bulan.</small></td></tr>
                <tr><th class="w-25 text-right">Biaya Denda</th><th class="w-1">:</th><td class="w-74">Rp {{ formatMoney($data->denda) }} <small class="text-primary">Biaya denda karena terlambat dari waktu pembayaran. Nominal denda dikali dengan bulan keterlambatan.</small></td></tr>
                <tr><th class="w-25 text-right">Tarif 1 (0 - {{ $data->volume_1 }}m<sup>3</sup>)</th><th class="w-1">:</th><td class="w-74">Rp {{ formatMoney($data->tarif_1) }} <small class="text-primary">Tarif per m<sup>3</sup> untuk pemakaian air antara 0 - {{ $data->volume_1 }} m<sup>3</sup>.</small></td></tr>
                <?php $v = $data->volume_1; ?>
                <tr><th class="w-25 text-right">Tarif 2 ({{ $v }} - {{ $v + $data->volume_2 }}m<sup>3</sup>)</th><th class="w-1">:</th><td class="w-74">Rp {{ formatMoney($data->tarif_2) }} <small class="text-primary">Tarif per m<sup>3</sup> untuk pemakaian air antara {{ $v }} - {{ $v + $data->volume_2 }} m<sup>3</sup>.</small></td></tr>
                <?php $v += $data->volume_2; ?>
                <tr><th class="w-25 text-right">Tarif 3 ({{ $v }} - {{ $v + $data->volume_3 }}m<sup>3</sup>)</th><th class="w-1">:</th><td class="w-74">Rp {{ formatMoney($data->tarif_3) }} <small class="text-primary">Tarif per m<sup>3</sup> untuk pemakaian air antara {{ $v }} - {{ $v + $data->volume_3 }} m<sup>3</sup>.</small></td></tr>
                <?php $v += $data->volume_3; ?>
                <tr><th class="w-25 text-right">Tarif 4 ({{ $v }} - {{ $v + $data->volume_4 }}m<sup>3</sup>)</th><th class="w-1">:</th><td class="w-74">Rp {{ formatMoney($data->tarif_4) }} <small class="text-primary">Tarif per m<sup>3</sup> untuk pemakaian air antara {{ $v }} - {{ $v + $data->volume_4 }} m<sup>3</sup>.</small></td></tr>
                <?php $v += $data->volume_4; ?>
                <tr><th class="w-25 text-right">Tarif 5 ( lebih dari {{ $v }} m<sup>3</sup>)</th><th class="w-1">:</th><td class="w-74">Rp {{ formatMoney($data->tarif_5) }} <small class="text-primary">Tarif per m<sup>3</sup> untuk pemakaian air lebih dari {{ $v }} m<sup>3</sup>.</small></td></tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    unblockUI();
});
</script>
