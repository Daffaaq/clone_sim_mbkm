<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;

class MitraExport implements FromView, WithTitle, WithStyles, ShouldAutoSize
{
    public $mitra;

    public function __construct($mitra)
    {
        $this->mitra = $mitra;
    }

    public function styles($sheet)
    {
        $last_index = count($this->mitra);
        $sheet->getStyle('A1:D' . $last_index + 1)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('A1:D1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');

        return [
            1 => ['font' => ['bold' => true]]
        ];
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('report.daftar_mitra.export', [
            'mitra' => $this->mitra
        ]);
    }

    public function title(): string
    {
        return "Daftar Mitra";
    }
}
