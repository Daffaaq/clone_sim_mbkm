<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{!! $page->title !!}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
        <form id="form-nilai" action="{{ route('simpan.nilai.pembahas') }}" method="POST">
            <div class="modal-body p-0">
                @csrf
                <div id="error-message" class="alert alert-warning" style="display: none;"></div>
                <div id="success-message" class="alert alert-success" style="display: none; text-align: center;"></div>
                <input type="hidden" name="periode_id" value="{{ $activePeriods }}">
                <input type="hidden" name="semhas_daftar_id" value="{{ $semhas_daftar_id }}">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Kriteria Penilaian</th>
                            <th scope="col">Nilai (1-100)</th>
                            <th scope="col">Bobot Nilai</th>
                            <th scope="col">Nilai x Bobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kriteriaNilai as $nilai)
                            @php
                                // Cari nilai yang sesuai dari koleksi $datanilai
                                $nilaiModel = $datanilai->firstWhere(
                                    'nilai_pembahas_dosen_id',
                                    $nilai->nilai_pembahas_dosen_id,
                                );
                                // dd($nilaiModel);
                                $nilaiValue = $nilaiModel ? $nilaiModel->nilai : '';
                            @endphp
                            <tr>
                                <td>{{ $nilai->name_kriteria_pembahas_dosen }}</td>
                                <td>
                                    <input type="number" id="nilai_{{ $nilai->id }}"
                                        name="nilai[{{ $nilai->id }}]"
                                        class="form-control form-control-sm nilai-subkriteria"
                                        value="{{ $nilaiValue }}"
                                        {{ $nilai->subKriteria->isEmpty() ? '' : 'readonly' }} required>
                                    <input type="hidden" name="nilai_pembahas_dosen_id[{{ $nilai->id }}]"
                                        value="{{ $nilai->nilai_pembahas_dosen_id }}">
                                </td>
                                <td>{{ $nilai->bobot }}</td>
                                <td id="nilai_x_bobot_{{ $nilai->id }}"></td>
                            </tr>
                        @endforeach
                        <tr class="total-nilai-row">
                            <td colspan="3" class="total-nilai" style="text-align: center;">Total Nilai</td>
                            <td class="total-nilai1"></td>
                        </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <div class="form-group" style="display: flex;align-items: center;margin-bottom: 10px;">
                                <label for="saran_pembahas_dosen"
                                    style="flex: 0 0 200px; margin-right: 10px; text-align: right;">Saran
                                    pembahas Dosen</label>
                                <textarea class="form-control" id="saran_pembahas_dosen" name="saran_pembahas_dosen" rows="3"
                                    style="flex: 1; width: 590px;"><?php echo isset($existingNilai) ? $existingNilai->saran_pembahas_dosen : ''; ?></textarea>

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="form-group" style="display: flex;align-items: center;margin-bottom: 10px;">
                                <label for="catatan_pembahas_dosen"
                                    style="flex: 0 0 200px; margin-right: 10px; text-align: right;">Catatan
                                    pembahas Dosen</label>
                                <textarea class="form-control" id="catatan_pembahas_dosen" name="catatan_pembahas_dosen" rows="3"
                                    style=" flex: 1; width: 590px"><?php echo isset($existingNilai) ? $existingNilai->catatan_pembahas_dosen : ''; ?></textarea>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        var nilaiInputs = document.querySelectorAll('.nilai-subkriteria');
        var average = 0;
        var form = document.getElementById('form-nilai');
        var inputsValid = true;

        function hitungNilai() {
            var totalNilai = 0;

            nilaiInputs.forEach(function(input) {
                var nilai = parseFloat(input.value);
                var bobotCell = input.closest('tr').querySelector('td:nth-child(3)');
                var bobot = parseFloat(bobotCell.textContent);
                var nilaiXBobotCell = input.closest('tr').querySelector('td:nth-child(4)');

                if (!isNaN(nilai) && !isNaN(bobot) && nilaiXBobotCell) {
                    var nilaiXBobot = nilai * bobot;
                    nilaiXBobotCell.textContent = nilaiXBobot;
                }

                // Hitung total nilai
                var nilaiXBobot = parseFloat(nilaiXBobotCell.textContent);
                if (!isNaN(nilaiXBobot)) {
                    totalNilai += nilaiXBobot;
                }
            });

            // Tampilkan total nilai
            document.querySelector('.total-nilai1').textContent = totalNilai;
        }

        // Hitung nilai saat halaman dimuat
        hitungNilai();
        nilaiInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                var nilai = parseFloat(input.value);
                console.log("Nilai: " + nilai);
                // Traverse the DOM to find the bobot value in the same row
                var bobotCell = input.closest('tr').querySelector('td:nth-child(3)');
                var bobot = parseFloat(bobotCell.textContent);
                console.log("Bobot: " + bobot);

                // Access the <td> element where we want to display the calculated value
                var nilaiXBobotCell = input.closest('tr').querySelector('td:nth-child(4)');
                console.log("Nilai x Bobot Cell: " + nilaiXBobotCell);

                if (!isNaN(nilai) && !isNaN(bobot) && nilaiXBobotCell) {
                    // Hitung nilai x bobot
                    var nilaiXBobot = nilai * bobot;
                    console.log("Nilai x Bobot: " + nilaiXBobot);
                    nilaiXBobotCell.textContent = nilaiXBobot;
                } else if (!isNaN(nilai) && isNaN(bobot) && nilaiXBobotCell) {
                    // Jika bobot tidak valid, hitung rata-rata nilai
                    var totalNilai = 0;
                    var jumlahInput = 0;

                    nilaiInputs.forEach(function(innerInput) {
                        var innerNilai = parseFloat(innerInput.value);

                        var innerBobotCell = innerInput.closest('tr').querySelector(
                            'td:nth-child(3)');
                        var innerBobot = parseFloat(innerBobotCell.textContent);

                        if (!isNaN(innerNilai) && isNaN(innerBobot)) {
                            totalNilai += innerNilai;
                            jumlahInput++;
                        }
                    });

                    if (jumlahInput !== 0) {
                        // Hitung rata-rata nilai
                        average = totalNilai / jumlahInput;
                        console.log("Rata-rata: " + average);

                        // Temukan semua input yang dinonaktifkan (tanpa bobot)
                        var disabledInputs = document.querySelectorAll(
                            'td:nth-child(2) input[readonly].form-control.form-control-sm.nilai-subkriteria'
                        );

                        disabledInputs.forEach(function(disabledInput) {
                            // Perbarui nilai input yang dinonaktifkan dengan nilai rata-rata
                            disabledInput.value = average;
                            // Mendapatkan nilai x bobot cell
                            var nilaiXBobotCell = disabledInput.closest('tr')
                                .querySelector('td:nth-child(4)');

                            // Hitung dan perbarui nilai x bobot menggunakan nilai rata-rata
                            var nilaiXBobot = average * parseFloat(nilaiXBobotCell
                                .previousElementSibling.textContent);
                            nilaiXBobotCell.textContent = nilaiXBobot;
                        });
                    } else {
                        console.log("Tidak ada input yang memiliki bobot NaN.");
                    }
                } else {
                    console.log("Nilai dan bobot harus berupa angka.");
                }
                // Hitung total nilai
                var totalNilai = 0;
                document.querySelectorAll('[id^=nilai_x_bobot_]').forEach(function(
                    cell) {
                    var value = parseFloat(cell.textContent);
                    console.log(value)
                    if (!isNaN(value)) {
                        totalNilai += value;
                    }
                });
                document.querySelector('.total-nilai1').textContent = totalNilai;
            });
        });
        // form.addEventListener('submit', function(event) {
        //     event.preventDefault(); // Prevent the default form submission

        //     var formData = new FormData(form); // Create a FormData object to store form data
        //     console.log(formData);
        //     var $modal = $('#modal-master');
        //     // Kirim formulir menggunakan Fetch API
        //     fetch(form.action, {
        //             method: 'POST',
        //             body: formData
        //         })
        //         .then(response => {
        //             if (response.ok) {
        //                 // Handle successful response here
        //                 console.log('Nilai berhasil disimpan');
        //                 $modal.one('shown.bs.modal', function() {
        //                     $modal.modal('hide');
        //                 });
        //             } else {
        //                 // Handle error response here
        //                 console.error('Terjadi kesalahan saat menyimpan nilai');
        //             }
        //         })
        //         .catch(error => {
        //             // Handle fetch error here
        //             console.error('Terjadi kesalahan saat mengirim permintaan', error);
        //         });
        // });
        function closeSuccessModal() {
            console.log("Closing success modal...");
            // Sembunyikan pesan sukses
            $('#success-message').fadeOut('slow');
            // Tutup modal
            $('#modal-master').modal('hide');
        }
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            var valid = true;
            nilaiInputs.forEach(function(input) {
                var nilai = parseFloat(input.value);
                if (isNaN(nilai) || nilai < 51 || nilai > 100) {
                    // Menampilkan pesan kesalahan jika nilai tidak sesuai kriteria
                    $('#error-message').text(
                        'Nilai harus di antara 51 dan 100'
                    ).show();
                    setTimeout(function() {
                        $('#error-message').fadeOut('slow');
                    }, 5000);
                    valid =
                        false; // Set variabel valid menjadi false jika ada nilai tidak valid
                    return false; // Stop the iteration jika ada nilai tidak valid
                }
            });
            if (valid) {
                var formData = new FormData(form);
                var $modal = $('#modal-master');

                $.ajax({
                    url: form.action,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Nilai berhasil disimpan');
                        $('#success-message').text('Data berhasil disimpan').show();
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        console.error('Terjadi kesalahan saat menyimpan nilai', error);
                    }
                });
            }
        });
    });
</script>
