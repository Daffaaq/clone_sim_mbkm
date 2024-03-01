@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-xs-12">
                <div class="card card-outline card-{{ $theme->card_outline }}">
                    <div class="card-header">
                        <h3 class="card-title mt-1">
                            <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                            Rekap Dosen Per Prodi
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Prodi</th>
                                <th class="text-right">Total Dosen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0 ?>
                            @foreach($data->dosen_prodi as $i=>$d)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $d->prodi_code }}</td>
                                    <td class="text-center pr-4">{{ formatMoney($d->total) }}</td>
                                </tr>
                                <?php $total += $d->total ?>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right" colspan="2">Total :</th>
                                <th class="text-center pr-4">{{ formatMoney($total) }}</th>
                            </tr>
                        </tfoot>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-9 col-xs-12">
                <div class="card card-outline card-{{ $theme->card_outline }}">
                    <div class="card-header">
                        <h3 class="card-title mt-1">
                            <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                            Rekap Mahasiswa Per Prodi yang Mengajukan Proposal Tugas Akhir
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Prodi</th>
                                <th class="text-center">Total Mhs</th>
                                <th class="text-center text-dark bg-success-opacity">Diterima</th>
                                <th class="text-center text-dark bg-danger-opacity">Ditolak</th>
                                <th class="text-center text-dark bg-warning-opacity">Menunggu</th>
                                <th class="text-center text-dark bg-secondary-opacity">Draft</th>
                                <th class="text-center text-dark bg-gray-dark-opacity">~NA~</th>
                                <th class="text-center">Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $total_mhs = 0;
                                $total_pro = 0;
                                $total_acc = 0;
                                $total_rej = 0;
                                $total_sub = 0;
                                $total_dra = 0;
                                $total_na = 0;
                                $total = 0;
                            ?>
                            @foreach($data->mahasiswa_prodi as $i=>$d)
                                <?php
                                    $sub_total = $d->acc + $d->rej + $d->sub + $d->dra;
                                ?>
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $d->prodi_code }}</td>
                                    <td class="text-center">{{ formatMoney($d->total) }}</td>
                                    <td class="text-center text-dark bg-success-opacity">{{ formatMoney($d->acc) }}</td>
                                    <td class="text-center text-dark bg-danger-opacity">{{ formatMoney($d->rej) }}</td>
                                    <td class="text-center text-dark bg-warning-opacity">{{ formatMoney($d->sub) }}</td>
                                    <td class="text-center text-dark bg-secondary-opacity">{{ formatMoney($d->dra) }}</td>
                                    <td class="text-center text-dark bg-gray-dark-opacity">{{ formatMoney($d->total - $sub_total) }}</td>
                                    <td class="text-center pr-4">{{ formatMoney($d->total) }}</td>
                                </tr>
                                <?php
                                    $total_mhs += $d->total;
                                    $total_pro += $sub_total;
                                    $total_acc += $d->acc;
                                    $total_rej += $d->rej;
                                    $total_sub += $d->sub;
                                    $total_dra += $d->dra;
                                    $total_na += ($d->total - $sub_total);
                                    $total += $d->total;
                                ?>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th class="text-right" colspan="2">Total :</th>
                                <th class="text-center">{{ formatMoney($total_mhs) }}</th>
                                <th class="text-center bg-success-opacity">{{ formatMoney($total_acc) }}</th>
                                <th class="text-center bg-danger-opacity">{{ formatMoney($total_rej) }}</th>
                                <th class="text-center bg-warning-opacity">{{ formatMoney($total_sub) }}</th>
                                <th class="text-center bg-secondary-opacity">{{ formatMoney($total_dra) }}</th>
                                <th class="text-center bg-gray-dark-opacity">{{ formatMoney($total_na) }}</th>
                                <th class="text-center pr-4">{{ formatMoney($total) }}</th>
                            </tr>
                            </tfoot>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('content-js')
<script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    var rekapitulasiChart;

    $(document).ready(function(){
        /*rekapitulasiChart = new Chart($('#rekapitulasiChart').get(0).getContext('2d'), {
            type: 'line',
            data: {
                labels: ["!! implode('","', $label) !!}"],
                datasets:[{
                        label: "Deposit",
                        data: [!! implode(',', $deposit) !!}],
                        backgroundColor: "rgba(0, 162, 40, 0.2)",
                        borderColor: "rgb(0, 162, 40)",
                        fill: false,
                        borderWidth: 1
                    }, {
                        label: "Withdraw",
                        data: [!! implode(',', $withdraw) !!}],
                        backgroundColor: "rgba(229, 3, 5, 0.2)",
                        borderColor: "rgb(229, 3, 5)",
                        fill: false,
                        borderWidth: 1
                    }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                datasetFill: true,
                legend: {
                    display: true
                },
                scaleStartValue: 0,
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            suggestedMin: 0,
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return parseInt(value).toLocaleString('id-ID');
                            }
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].label+" : Rp " +parseInt(tooltipItem.yLabel).toLocaleString('id-ID');
                        }
                    },
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    onHover: chartOnHover,
                    mode: 'nearest',
                    intersect: true
                }
            }
        });*/
    });
</script>
@endpush
