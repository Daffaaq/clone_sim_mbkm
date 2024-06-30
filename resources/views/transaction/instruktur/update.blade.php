@php
    setlocale(LC_TIME, 'id_ID');
    \Carbon\Carbon::setLocale('id');
@endphp

@extends('layouts.template')

@section('content')
    <style>
        /* CSS untuk memberi latar belakang semi-transparan pada modal content */
        .modal-content {
            background-color: transparent;
            /* Adjust opacity (0.8) as needed */
            /* Optional: Add a subtle shadow */
        }

        .loading-modal-open {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(8px);
            /* Adjust the blur intensity as needed */
            -webkit-backdrop-filter: blur(8px);
            /* For Safari */
            background-color: rgba(0, 0, 0, 0.4);
            /* Adjust the opacity as needed */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        #loadingModal {
            align-items: center;
            justify-content: center;
            background-color: transparent;
            /* Ubah background modal menjadi transparan */
        }

        #loadingModal .modal-content {
            background-color: transparent;
            /* Hapus latar belakang modal */
            box-shadow: none;
            /* Hapus bayangan jika tidak diperlukan */
            border: none;
            /* Hapus border jika tidak diperlukan */
            text-align: center;
        }

        .modal-dialog {
            margin: auto;
        }


        #loadingModal img {
            width: 80px;
            /* Sesuaikan ukuran gambar loading */
            height: auto;
            margin-bottom: 10px;
            /* Sesuaikan margin bawah jika perlu */
        }


        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <div class="container-fluid" id="container-daftar">
        <div class="row">
            <section class="col-lg-12">
                <div class="card card-outline card-{{ $theme->card_outline }}">
                    <div class="card-header">
                        <h3 class="card-title mt-1">
                            <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                            {!! $page->title !!}
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        @if ($instruktur && $instruktur->is_active == 1)
                            <div class="form-message text-center p-2">
                                <div class="alert alert-success">Data Instruktur Lapangan Sudah terverifikasi</div>
                            </div>
                        @elseif($instruktur && $instruktur->is_active == 0)
                            <div class="form-message text-center p-2">
                                <div class="alert alert-danger">Data Instruktur Lapangan Belum terverifikasi</div>
                            </div>
                        @else
                            <div class="form-message text-center p-2">
                                <div class="alert alert-danger">Data Instruktur Lapangan Belum dibuat</div>
                            </div>
                        @endif
                        <div class="form-message text-center"></div>
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th class="w-15 text-right">Magang ID</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">{{ $magang->magang_kode }}</td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Nama Kegiatan</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">{{ $magang->mitra->kegiatan->kegiatan_nama }}</td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Nama Mitra</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <i class="far fa-building text-md text-primary"></i>
                                        {{ $magang->mitra->mitra_nama }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Periode</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">{{ $magang->periode->periode_nama }}</td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Durasi</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <i class="far fa-clock text-md text-primary"></i>
                                        {{ $magang->mitra->mitra_durasi }}
                                        bulan
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Skema</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">{{ $magang->magang_skema }}</td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Batas Pendaftaran</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <i class="far fa-calendar-alt text-md text-primary"></i>
                                        {{ \Carbon\Carbon::parse($magang->mitra->mitra_batas_pendaftaran)->format('d M Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Anggota</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84">
                                        <table class="table table-sm text-sm table-bordered"
                                            style="table-layout:fixed;width:100%;" id="table-mhs">
                                            <thead>
                                                <tr>
                                                    <th style="width: 14%">No</th>
                                                    <th style="width: 22%">NIM</th>
                                                    <th style="width: 45%">Nama Mahasiswa</th>
                                                    <th style="width: 14%">Kelas</th>
                                                    <th style="width: 14%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($anggota as $key => $a)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $a->mahasiswa->nim }}</td>
                                                        <td>{{ $a->mahasiswa->nama_mahasiswa }}@if ($a->magang_tipe == 0)
                                                                <span class="badge badge-pill badge-primary">Ketua</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $a->mahasiswa->kelas }}</td>
                                                        <td>
                                                            @if ($a->magang_tipe == 1)
                                                                @if ($a->is_accept == 0)
                                                                    <span class="badge badge badge-warning">Menunggu</span>
                                                                @elseif ($a->is_accept == 1)
                                                                    <span class="badge badge badge-success">Menerima</span>
                                                                @elseif ($a->is_accept == 2)
                                                                    <span class="badge badge badge-danger">Menolak</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-15 text-right">Instruktur Lapangan</th>
                                    <th class="w-1">:</th>
                                    <td class="w-84 py-2">
                                        @if ($instruktur && $instruktur->is_active == 1)
                                            <!-- Tampilkan informasi bahwa mahasiswa sudah memiliki instruktur lapangan -->
                                            <div class="alert alert-info">
                                                {{ $instruktur->instruktur->nama_instruktur }}
                                            </div>
                                        @elseif($instruktur && $instruktur->is_active == 0)
                                            <form method="post" action="{{ route('resend_verification') }}" role="form"
                                                class="form-horizontal" id="form" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="instruktur_id"
                                                    value="{{ $instruktur->instruktur_id }}">

                                                <!-- Tambahkan input untuk data instruktur -->
                                                <div class="form-group required">
                                                    <label for="nama_instruktur" class="control-label">Nama
                                                        Instruktur</label>
                                                    <input type="text" class="form-control" id="nama_instruktur"
                                                        name="nama_instruktur"
                                                        value="{{ $instruktur->nama_instruktur ?? '' }}">
                                                    <small id="excel" class="form-text" style="margin-left: 0px;">Nama
                                                        Pembimbing Lapangan</small>
                                                    <span id="valid-message-nama_instruktur" class="text-danger"
                                                        style="display: none;">Kolom ini harus diisi.</span>
                                                </div>
                                                <div class="form-group required">
                                                    <label for="instruktur_email" class="control-label">Email
                                                        Instruktur</label>
                                                    <input type="email" class="form-control" id="instruktur_email"
                                                        name="instruktur_email"
                                                        value="{{ $instruktur->instruktur_email ?? '' }}">
                                                    <small id="excel" class="form-text" style="margin-left: 0px;">Email
                                                        Pembimbing Lapangan harus
                                                        valid</small>
                                                    <span id="valid-message-instruktur_email" class="text-danger"
                                                        style="display: none;">Kolom ini harus diisi.</span>
                                                </div>
                                                <div class="form-group required">
                                                    <label for="instruktur_phone" class="control-label">Nomor Telepon
                                                        Instruktur</label>
                                                    <input type="text" class="form-control" id="instruktur_phone"
                                                        name="instruktur_phone"
                                                        value="{{ $instruktur->instruktur_phone ?? '' }}">
                                                    <small id="excel" class="form-text"
                                                        style="margin-left: 0px;">Format nomor telepon pembimbing Lapangan
                                                        tidak valid harus dimulai dengan 62 dan memiliki panjang 8-13 digit
                                                        angka</small>
                                                    <span id="valid-message-instruktur_phone" class="text-danger"
                                                        style="display: none;">Kolom ini harus diisi.</span>
                                                </div>
                                                <div class="form-group">
                                                    <label for="password" class="control-label">Password</label>
                                                    <input type="password" class="form-control" id="passwordresend"
                                                        name="password" value="">
                                                    <small id="excel" class="form-text"
                                                        style="margin-left: 0px;">Password minimal 8 karakter(boleh
                                                        kosong)</small>
                                                    <span id="valid-message-password" class="text-danger"
                                                        style="display: none;">Kolom ini harus diisi.</span>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Kirim Ulang
                                                    Verifikasi</button>
                                            </form>
                                        @else
                                            <form method="post" action="{{ route('create_instruktur') }}"
                                                role="form" class="form-horizontal" id="form-sb"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="magang_id" value="{{ $magang->magang_id }}">
                                                {{-- @foreach ($anggotas as $anggota)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="mahasiswa_id[]"
                                                            value="{{ $anggota->mahasiswa->mahasiswa_id }}">
                                                        <label
                                                            class="form-check-label">{{ $anggota->mahasiswa->nama_mahasiswa }}</label>
                                                    </div>
                                                @endforeach --}}
                                                <div class="checkbox-container"
                                                    style="display: flex; flex-direction: column; gap: 10px;">
                                                    @foreach ($anggotas as $anggota)
                                                        <div class="form-check"
                                                            style="display: flex; align-items: center; padding: 5px; border: 1px solid #ccc; border-radius: 5px;">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="mahasiswa_id[]"
                                                                value="{{ $anggota->mahasiswa->mahasiswa_id }}"
                                                                id="mahasiswa-{{ $anggota->mahasiswa->mahasiswa_id }}"
                                                                style="margin-right: 10px;">
                                                            <label class="form-check-label"
                                                                for="mahasiswa-{{ $anggota->mahasiswa->mahasiswa_id }}"
                                                                style="cursor: pointer;">{{ $anggota->mahasiswa->nama_mahasiswa }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <span id="validation-message" class="text-danger"
                                                    style="display: none;">Mohon pilih setidaknya satu mahasiswa.</span>
                                                <!-- Tambahkan input untuk data instruktur -->
                                                <div class="form-group required">
                                                    <label for="nama_instruktur" class="control-label">Nama
                                                        Instruktur</label>
                                                    <input type="text" class="form-control" id="nama_instruktur"
                                                        name="nama_instruktur">
                                                    <small id="excel" class="form-text"
                                                        style="margin-left: 0px;">Nama
                                                        Pembimbing Lapangan</small>
                                                    <span id="valid-message-nama_instruktur" class="text-danger"
                                                        style="display: none;">Kolom ini harus diisi.</span>
                                                </div>
                                                <div class="form-group required">
                                                    <label for="instruktur_email" class="control-label">Email
                                                        Instruktur</label>
                                                    <input type="email" class="form-control" id="instruktur_email"
                                                        name="instruktur_email">
                                                    <small id="excel" class="form-text"
                                                        style="margin-left: 0px;">email
                                                        Pembimbing Lapangan harus valid</small>
                                                    <span id="valid-message-instruktur_email" class="text-danger"
                                                        style="display: none;">Kolom ini harus diisi.</span>
                                                </div>
                                                <div class="form-group required">
                                                    <label for="instruktur_phone" class="control-label">Nomor Telepon
                                                        Instruktur</label>
                                                    <input type="text" class="form-control" id="instruktur_phone"
                                                        name="instruktur_phone">
                                                    <small id="excel" class="form-text"
                                                        style="margin-left: 0px;">Format nomor telepon pembimbing Lapangan
                                                        tidak valid harus dimulai dengan 62 dan memiliki panjang 8-13 digit
                                                        angka</small>
                                                    <span id="valid-message-instruktur_phone" class="text-danger"
                                                        style="display: none;">Kolom ini harus diisi.</span>
                                                </div>
                                                <div class="form-group required">
                                                    <label for="password" class="control-label">Password</label>
                                                    <input type="password" class="form-control" id="password"
                                                        name="password">
                                                    <small id="excel" class="form-text"
                                                        style="margin-left: 0px;">Password minimal 8</small>
                                                    <span id="valid-message-password" class="text-danger"
                                                        style="display: none;">Kolom ini harus diisi.</span>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="align-items: center">
                <div class="modal-body text-center">
                    <img src="{{ asset('assets/ZKZg.gif') }}" alt="Loading animation">
                </div>
            </div>
        </div>
    </div>

@endsection
@push('content-js')
    <script>
        $(document).ready(function() {
            function showLoadingModal() {
                $('#loadingModal').modal({
                    backdrop: 'static', // Prevent dismissing on backdrop click
                    keyboard: false // Prevent dismissing with Esc key
                });
                $('body').addClass('loading-modal-open'); // Add class to blur background
            }

            function hideLoadingModal() {
                $('#loadingModal').modal('hide');
                $('body').removeClass('loading-modal-open'); // Remove blur effect
            }

            function validateMahasiswaSelection() {
                var selectedMahasiswa = $('input[name="mahasiswa_id[]"]:checked').length;
                if (selectedMahasiswa === 0) {
                    $('#validation-message').show(); // Tampilkan pesan validasi
                    return false; // Batalkan pengiriman formulir
                } else {
                    $('#validation-message').hide(); // Sembunyikan pesan validasi jika valid
                    return true; // Lanjutkan pengiriman formulir
                }
            }

            function validatePhoneNumber(phoneNumber) {
                var phoneRegex =
                    /^62\d{8,13}$/; // regex untuk nomor telepon, harus dimulai dengan 62 dan memiliki panjang 8-13 digit angka
                return phoneRegex.test(phoneNumber);
            }

            // Validasi password minimal 8 karakter
            function validatePassword(password) {
                return password.length >= 8;
            }

            function validatePasswordresend(password) {
                return password === '' || password.length >= 8;
            }
            // Submit form
            $('#form').submit(function(event) {
                event.preventDefault(); // Prevent default form submission

                var valid = true;

                $('.form-group.required').each(function() {
                    var input = $(this).find('input');
                    var messageId = $(this).find('span').attr('id');
                    if (input.val() === '') {
                        $('#' + messageId).show(); // Tampilkan pesan validasi
                        valid = false;
                    } else {
                        $('#' + messageId).hide(); // Sembunyikan pesan validasi jika valid
                    }
                });

                // Validasi nomor telepon
                var phoneNumberInput = $('#instruktur_phone');
                var phoneNumber = phoneNumberInput.val();
                if (!validatePhoneNumber(phoneNumber)) {
                    $('#valid-message-instruktur_phone').text('Format nomor telepon tidak valid');
                    $('#valid-message-instruktur_phone').show();
                    valid = false;
                } else {
                    $('#valid-message-instruktur_phone').hide();
                }
                var passwordInput = $('#passwordresend');
                var password = passwordInput.val();
                if (!validatePasswordresend(password)) {
                    $('#valid-message-password').text('Password minimal 8 karakter');
                    $('#valid-message-password').show();
                    valid = false;
                } else {
                    $('#valid-message-password').hide();
                }

                if (!valid) {
                    return; // Batalkan pengiriman formulir jika validasi gagal
                }
                showLoadingModal();
                // Send AJAX request to submit form
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function(response) {
                        setTimeout(() => {
                            hideLoadingModal();
                        }, 5000);
                        // Reload the page after successful form submission
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        setTimeout(() => {
                            hideLoadingModal();
                        }, 5000);
                        // Tangani kesalahan jika permintaan AJAX gagal
                        console.error(error);
                        handleResponse(response);
                    }
                });
            });
            $('#form-sb').submit(function(event) {
                event.preventDefault(); // Prevent default form submission
                if (!validateMahasiswaSelection()) {
                    return; // Batalkan pengiriman formulir jika validasi gagal
                }

                var valid = true;
                $('.form-group.required').each(function() {
                    var input = $(this).find('input');
                    var messageId = $(this).find('span').attr('id');
                    if (input.val() === '') {
                        $('#' + messageId).show(); // Tampilkan pesan validasi
                        valid = false;
                    } else {
                        $('#' + messageId).hide(); // Sembunyikan pesan validasi jika valid
                    }
                });

                // Validasi nomor telepon
                var phoneNumberInput = $('#instruktur_phone');
                var phoneNumber = phoneNumberInput.val();
                if (!validatePhoneNumber(phoneNumber)) {
                    $('#valid-message-instruktur_phone').text('Format nomor telepon tidak valid');
                    $('#valid-message-instruktur_phone').show();
                    valid = false;
                } else {
                    $('#valid-message-instruktur_phone').hide();
                }

                // Validasi password
                var passwordInput = $('#password');
                var password = passwordInput.val();
                if (!validatePassword(password)) {
                    $('#valid-message-password').text('Password minimal 8 karakter');
                    $('#valid-message-password').show();
                    valid = false;
                } else {
                    $('#valid-message-password').hide();
                }

                if (!valid) {
                    return; // Batalkan pengiriman formulir jika validasi gagal
                }
                showLoadingModal();
                // Send AJAX request to submit form
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function(response) {
                        setTimeout(() => {
                            hideLoadingModal();
                        }, 5000);
                        // Reload the page after successful form submission
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        setTimeout(() => {
                            hideLoadingModal();
                        }, 5000);
                        // Tangani kesalahan jika permintaan AJAX gagal
                        console.error(error);
                        handleResponse(response);
                    }
                });
            });
        });
    </script>
@endpush
