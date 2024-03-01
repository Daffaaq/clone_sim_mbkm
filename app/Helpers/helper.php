<?php

function getJurusanID()
{
    $user = \Illuminate\Support\Facades\Auth::user();
    switch ($user->group_id) {
        case 3:
            return getDosen()->jurusan_id;
            break;
        case 4:
            return getMahasiswa()->jurusan_id;
            break;
        case 1:
        case 2:
        default:
            return 1;
            break;
    }
}

function getProdiID()
{
    $user = \Illuminate\Support\Facades\Auth::user();
    switch ($user->group_id) {
        case 3:
            return getDosen()->prodi_id;
            break;
        case 4:
            return getMahasiswa()->prodi_id;
            break;
        case 1:
        case 2:
        default:
            return 1;
            break;
    }
}

function getDosen()
{
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user->group_id == 3) {
        $dosen = session()->get('dosen');

        return ($dosen) ?: $user->getUserDosen;
    } else {
        return null;
    }
}

function getDosenID()
{
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user->group_id == 3) {
        $dosen = session()->get('dosen');

        return ($dosen) ? $dosen->dosen_id : $user->getUserDosen->dosen_id;
    } else {
        return null;
    }
}

function getMahasiswa()
{
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user->group_id == 4) {
        $mahasiswa = session()->get('mahasiswa');

        return ($mahasiswa) ?: $user->getUserMahasiswa;
    } else {
        return null;
    }
}

function getMahasiswaID()
{
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user->group_id == 4) {
        $mahasiswa = session()->get('mahasiswa');

        return ($mahasiswa) ? $mahasiswa->mahasiswa_id : $user->getUserMahasiswa->mahasiswa_id;
    } else {
        return null;
    }
}

function getPerusahaan()
{
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user->group_id == 5) {
        $perusahaan = session()->get('perusahaan');

        return ($perusahaan) ?: $user->getUserPerusahaan;
    } else {
        return null;
    }
}

function getPerusahaanID()
{
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user->group_id == 5) {
        $perusahaan = session()->get('perusahaan');

        return ($perusahaan) ? $perusahaan->perusahaan_id : $user->getUserPerusahaan->perusahaan_id;
    } else {
        return null;
    }
}

function getPeriodeID()
{
    return session()->get('periode')->periode_id;
}

function getPeriodeName()
{
    return session()->get('periode')->periode_name;
}

function checkDosenStatus($code = '')
{
    $s = [
        'AK' => 'Aktif',
        'IB' => 'Izin Belajar',
        'TB' => 'Tugas Belajar',
        'CT' => 'Cuti',
        'NA' => 'Lainnya',
    ];

    return (array_key_exists($code, $s)) ? $code : null;
}

function getJabatan($code = '', $with_style = false)
{
    $s = [
        1 => 'Tenaga Pengajar/CPNS/Kontrak',
        2 => 'Asisten Ahli',
        3 => 'Lektor',
        4 => 'Lektor Kepala',
        5 => 'Guru Besar',
    ];
    if ($with_style) {
        $s = [
            1 => '<span class="badge badge-secondary">TP/Kontrak/CPNS</span>',
            2 => '<span class="badge badge-success">Asisten Ahli</span>',
            3 => '<span class="badge badge-success">Lektor</span>',
            4 => '<span class="badge badge-success">Lektor Kepala</span>',
            5 => '<span class="badge badge-success">Guru Besar</span>',
        ];
    }
    return (array_key_exists($code, $s)) ? $s[$code] : '';
}

function getDosenStatus($code = '', $with_style = false)
{
    $s = [
        'AK' => 'Aktif',
        'IB' => 'Izin Belajar',
        'TB' => 'Tugas Belajar',
        'CT' => 'Cuti',
        'NA' => 'Lainnya',
    ];
    if ($with_style) {
        $s = [
            'AK' => '<span class="badge badge-success">Aktif</span>',
            'IB' => '<span class="badge badge-primary">Izin Belajar</span>',
            'TB' => '<span class="badge badge-info">Tugas Belajar</span>',
            'CT' => '<span class="badge badge-warning">Cuti</span>',
            'NA' => '<span class="badge badge-danger">Lainnya</span>',
        ];
    }
    return (array_key_exists($code, $s)) ? $s[$code] : '';
}

function getDosenStatusList()
{
    return json_decode(json_encode([
        ['id' => 'AK', 'name' => 'Aktif'],
        ['id' => 'IB', 'name'  => 'Izin Belajar'],
        ['id' => 'TB', 'name'  => 'Tugas Belajar'],
        ['id' => 'CT', 'name'  => 'Cuti'],
        ['id' => 'NA', 'name'  => 'Lainnya'],
    ]), false);
}

function checkDosenJenis($code = '')
{
    $s = [
        'P' => 'PNS',
        'T' => 'Tetap Non-PNS',
        'K' => 'Kontrak',
        'L' => 'Luar Biasa',
        'X' => 'Lainnya',
    ];

    return (array_key_exists($code, $s)) ? $code : null;
}

function getDosenJenis($code = '', $with_style = false)
{
    $s = [
        'P' => 'PNS',
        'T' => 'Tetap Non-PNS',
        'K' => 'Kontrak',
        'L' => 'Luar Biasa',
        'X' => 'Lainnya',
    ];
    if ($with_style) {
        $s = [
            'P' => '<span class="badge badge-success">PNS</span>',
            'T' => '<span class="badge badge-primary">Tetap Non-PNS</span>',
            'K' => '<span class="badge badge-info">Kontrak</span>',
            'L' => '<span class="badge badge-warning">Luar Biasa</span>',
            'X' => '<span class="badge badge-danger">Lainnya</span>',
        ];
    }
    return (array_key_exists($code, $s)) ? $s[$code] : '';
}

function getDosenJenisList()
{
    return json_decode(json_encode([
        ['id' => 'P', 'name' => 'PNS'],
        ['id' => 'T', 'name'  => 'Tetap Non-PNS'],
        ['id' => 'K', 'name'  => 'Kontrak'],
        ['id' => 'L', 'name'  => 'Luar Biasa'],
        ['id' => 'X', 'name'  => 'Lainnya'],
    ]), false);
}

function getMetodeBimbingan($code = '', $with_style = false)
{
    $s = [
        'OF' => 'Offline',
        'OZ' => 'Online Zoom',
        'WA' => 'Online Whatsapp',
    ];
    if ($with_style) {
        $s = [
            'OF' => '<span class="badge bg-purple">Offline</span>',
            'OZ' => '<span class="badge badge-primary">Online Zoom</span>',
            'WA' => '<span class="badge badge-success">Whatsapp</span>',
        ];
    }
    return (array_key_exists($code, $s)) ? $s[$code] : '';
}


function escapeHTMLString($string = '')
{
    return preg_replace('/\s+/', ' ',  trim(strip_tags($string)));
}

function e_debug($data = null, $dump = false)
{
    echo '<pre>';
    if ($dump) {
        var_dump($data);
    } else {
        print_r($data);
    }
    echo '</pre>';
}

function x_debug($data = null, $dump = false)
{
    e_debug($data, $dump);
    die;
}

function getLoadedFiles()
{
    echo '<pre>';
    print_r(get_included_files());
    echo '</pre>';
}

function start_time()
{
    $mtime = microtime();
    $mtime = explode(' ', $mtime);
    return $mtime;
}

function finish_time($starttime)
{
    $starttime = $starttime[1] + $starttime[0];
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    return ($mtime - $starttime);
}

function getServerDate($day = true, $time = false)
{
    $hari  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $date = '';
    if ($day) {
        $date .= $hari[date('w')] . ', ';
    }
    $date .= date('j') . ' ' . $bulan[date('n') - 1] . ' ' . date('Y');
    if ($time) {
        $date .= "&nbsp;&nbsp;&nbsp;" . date("G:i:s");
    }
    return $date;
}

function getTimeInterval($start, $finish)
{
    $start = new DateTime($start);
    $finish = new DateTime($finish);
    return $finish->getTimestamp() - $start->getTimestamp();
}

function trim_string($string, $only_alphanumeric = false)
{
    $res = '';
    if ($only_alphanumeric) {
        $res = preg_replace("/[^a-zA-Z0-9\s]/", "", $string);
    } else {
        $res = preg_replace("/[^a-zA-Z0-9:\/,._\-\s]/", "", $string);
    }
    return ($res) ? $res : null;
}

function trim_email($string)
{
    $res = preg_replace("/[^a-zA-Z0-9_.@]/", "", $string);
    return ($res) ? $res : null;
}

function trim_number($number)
{
    $res = preg_replace("/[^0-9]/", "", $number);
    return ($res) ? $res : null;
}

function getDropdownMonth($name = 'dd', $currentMonth = 1, $full = TRUE, $opt = null)
{
    $mon = ($full) ? array(1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember') :
        array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des');

    $dd = '<select id="' . $name . '" name="' . $name . '" ' . strip_tags($opt) . '>';
    foreach ($mon as $i => $m) {
        $dd .= '<option value="' . $i . '" ' . (($i == $currentMonth) ? 'selected' : '') . '>' . $m . '</option>';
    }
    $dd .= '</select>';
    return $dd;
}

function getDropdownYear($name = 'dd', $range = [], $currentYear = 1, $opt = null)
{
    $dd = '<select id="' . $name . '" name="' . $name . '" ' . strip_tags($opt) . '>';
    foreach ($range as $m) {
        $dd .= '<option value="' . $m . '" ' . (($m == $currentYear) ? 'selected' : '') . '>' . $m . '</option>';
    }
    $dd .= '</select>';
    return $dd;
}

function getMonthName($no = 1, $full = TRUE)
{
    $no = (int) $no;

    $month = array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    );
    $mon   = array(
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'May',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Aug',
        9 => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dec'
    );

    if ($full) {
        return (array_key_exists($no, $month)) ? $month[$no] : ' ';
    } else {
        return (array_key_exists($no, $mon)) ? $mon[$no] : ' ';
    }
}

function getDayName($day = null)
{
    //$day = ($date)? date('N', strtotime($date)) : date('N');
    switch ($day) {
        case '1':
            $day = 'Senin';
            break;
        case '2':
            $day = 'Selasa';
            break;
        case '3':
            $day = 'Rabu';
            break;
        case '4':
            $day = 'Kamis';
            break;
        case '5':
            $day = 'Jumat';
            break;
        case '6':
            $day = 'Sabtu';
            break;
        case '7':
            $day = 'Minggu';
            break;
    }
    return $day;
}

function getDateAgoFromToday(int $days_ago = 1, $format = 'Y-m-d')
{
    //return date($format, strtotime("-{$days_ago} days"));
    $date = new DateTime();
    return $date->modify("-{$days_ago} day")->format($format);
}

function netral_currency($money)
{
    return (float) str_replace('.', '', $money);
}

function netral_number($number)
{
    return (int) str_replace('.', '', $number);
}

function convertDate($date, $format = 'd M Y')
{
    $d = new DateTime($date);
    return $d->format($format);
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function convertValidDate($date, $source_format = 'd-m-Y', $destination_format = 'Y-m-d')
{
    if (validateDate($date, $source_format)) {
        //$date = convertDate($date, $destination_format);
        $date = DateTime::createFromFormat($source_format, $date);
        $date = $date->format($destination_format);
    } else {
        $date = date($destination_format);
    }
    return $date;
}

function formatIDR($money)
{
    $fmt = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
    $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
    //$fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0); untuk angkat puluhan/ratusan ribu, tiga digit 0 dibelakang akan hilang
    return $fmt->formatCurrency($money, "IDR");
}

function formatMoney($money)
{
    return number_format($money, 0, ',', '.');
}

function terbilang($angka)
{
    $terbF  = new NumberFormatter('id-ID', NumberFormatter::SPELLOUT);
    // 'id-ID' format penulisan bahasa id : Indonesia
    return $terbF->format($angka);
}
