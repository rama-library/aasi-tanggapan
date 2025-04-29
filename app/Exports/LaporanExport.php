<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanExport implements FromView, WithStyles, WithEvents
{
    protected $result;
    protected $document;
    protected $jenis;

    public function __construct($result, $document, $jenis)
    {
        $this->result = $result;
        $this->document = $document;
        $this->jenis = $jenis;
    }

    public function view(): View
    {
        return view('report.export_excel', [
            'result' => $this->result,
            'document' => $this->document,
            'jenis' => $this->jenis,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Header bold
        $lastCol = $this->jenis === 'full' ? 'J' : 'I';
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $lastCol = $this->jenis === 'full' ? 'J' : 'I';
                $highestRow = $sheet->getHighestRow();

                // Auto-size setiap kolom
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Center-align semua cell
                $sheet->getStyle("A1:{$lastCol}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center')
                    ->setWrapText(true);

                // Border untuk semua cell
                $sheet->getStyle("A1:{$lastCol}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Bold dan merge cell untuk header (baris 1-3)
                $sheet->mergeCells('A1:' . $lastCol . '1');
                $sheet->mergeCells('A2:' . $lastCol . '2');
                $sheet->mergeCells('A3:' . $lastCol . '3');

                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');
            },
        ];
    }
}
