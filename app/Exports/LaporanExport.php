<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanExport implements FromView, WithStyles, WithEvents, WithDrawings
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

                // Auto-size kolom
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Center align
                $sheet->getStyle("A1:{$lastCol}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center')
                    ->setWrapText(true);

                // Border semua cell
                $sheet->getStyle("A1:{$lastCol}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Merge & bold header
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->mergeCells("A3:{$lastCol}3");

                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');

                // Set tinggi baris default (untuk gambar agar muat)
                for ($row = 5; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(110);
                }
            },
        ];
    }

    public function drawings()
    {
        $drawings = [];
        $rowOffset = 6; // karena header 4 baris

        // Untuk lacak baris per batangtubuh
        $grouped = $this->result->groupBy(fn($item) => $item->batangtubuh->id ?? 0);
        $rowCounter = $rowOffset;

        foreach ($grouped as $group) {
            $item = $group->first();
            if ($item->batangtubuh && $item->batangtubuh->gambar) {
                $gambarPath = public_path('storage/' . $item->batangtubuh->gambar);
                if (file_exists($gambarPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Penjelasan');
                    $drawing->setDescription('Gambar Penjelasan');
                    $drawing->setPath($gambarPath);
                    $drawing->setHeight(100); // Sesuaikan tinggi
                    $drawing->setCoordinates('D' . $rowCounter); // kolom D = penjelasan
                    // Offset agar lebih ke tengah
                    $drawing->setOffsetX(40); // horizontal (default 0)
                    $drawing->setOffsetY(15); // vertical (default 0)
                    $drawings[] = $drawing;
                }
            }

            // Tambah baris sebanyak jumlah item di group
            $rowCounter += $group->count();
        }

        return $drawings;
    }
}
