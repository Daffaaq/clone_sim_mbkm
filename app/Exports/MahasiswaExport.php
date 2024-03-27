<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;

class MahasiswaExport implements FromView, WithTitle, WithStyles, ShouldAutoSize
{
    public $mahasiswa;

    public function __construct($mahasiswa)
    {
        $this->mahasiswa = $mahasiswa;
    }

    public function styles($sheet)
    {
        $last_index = count($this->mahasiswa);
        $sheet->getStyle('A1:L' . $last_index + 1)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('A1:L1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');

        return [
            1 => ['font' => ['bold' => true]]
        ];
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('report.daftar_mahasiswa.export', [
            'mahasiswa' => $this->mahasiswa
        ]);
    }

    public function title(): string
    {
        return "Daftar Mahasiswa";
    }
}
