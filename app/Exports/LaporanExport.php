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
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
        $lastCol = $this->jenis === 'full' ? 'I' : 'H';
        $sheet->getStyle("A5:{$lastCol}5")->getFont()->setBold(true);
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = $this->jenis === 'full' ? 'I' : 'H';
                $highestRow = $sheet->getHighestRow();
    
                /** 1️⃣ Tetapkan lebar kolom manual **/
                $columnWidths = [
                    'A' => 6,
                    'B' => 20,
                    'C' => 20,
                    'D' => 35,
                    'E' => 35,
                    'F' => 20,
                    'G' => 20,
                    'H' => 20,
                    'I' => 25,
                ];
                foreach ($columnWidths as $col => $width) {
                    if ($col <= $lastCol) {
                        $sheet->getColumnDimension($col)->setWidth($width);
                        $sheet->getColumnDimension($col)->setAutoSize(false);
                    }
                }
    
                /** 2️⃣ Wrap text + alignment **/
                $styleRange = "A1:{$lastCol}{$highestRow}";
                $sheet->getStyle($styleRange)->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)
                    ->setWrapText(true);
    
                /** 3️⃣ Border seluruh area data **/
                $sheet->getStyle("A5:{$lastCol}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
                /** 4️⃣ Header merge & center **/
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
                /** 5️⃣ Hitung tinggi otomatis berdasar teks (tanpa matikan wrap) **/
                for ($row = 6; $row <= $highestRow; $row++) {
                    $maxHeight = 18;
                    foreach (range('A', $lastCol) as $col) {
                        $cell = "{$col}{$row}";
                        $value = (string) $sheet->getCell($cell)->getValue();
                        if ($value !== '') {
                            // Perkirakan jumlah baris berdasarkan panjang teks dan lebar kolom
                            $colWidth = $sheet->getColumnDimension($col)->getWidth();
                            $charsPerLine = max(1, round($colWidth * 1.2)); // rasio empiris
                            $lines = ceil(mb_strlen($value) / $charsPerLine);
                            $height = 15 * max(1, $lines);
                            $maxHeight = max($maxHeight, $height);
                        }
                    }
                    $sheet->getRowDimension($row)->setRowHeight($maxHeight);
                }
    
                /** 6️⃣ Baris dengan gambar dibuat lebih tinggi **/
                for ($row = 6; $row <= $highestRow; $row++) {
                    $val = $sheet->getCell("C{$row}")->getValue();
                    if (empty($val)) {
                        $sheet->getRowDimension($row)->setRowHeight(
                            max($sheet->getRowDimension($row)->getRowHeight(), 100)
                        );
                    }
                }
    
                /** 7️⃣ Pastikan gridline & cell pertama aktif **/
                $sheet->setShowGridlines(true);
                $sheet->setSelectedCell('A1');
            },
        ];
    }

    public function drawings()
    {
        $drawings = [];

        /** Logo di kiri atas **/
        $logoPath = public_path('adminpage/assets/img/aasi.png');
        if (file_exists($logoPath)) {
            $logo = new Drawing();
            $logo->setName('Logo');
            $logo->setDescription('Logo AASI');
            $logo->setPath($logoPath);
            $logo->setHeight(70);
            $logo->setCoordinates('A1');
            $logo->setOffsetX(10);
            $logo->setOffsetY(5);
            $drawings[] = $logo;
        }

        /** Gambar batang tubuh **/
        $rowOffset = 6;
        $grouped = $this->result->groupBy(fn($item) => $item->batangtubuh->id ?? 0);
        $rowCounter = $rowOffset;

        foreach ($grouped as $group) {
            $item = $group->first();
            if ($item->batangtubuh && $item->batangtubuh->gambar) {
                $gambarPath = public_path('storage/' . $item->batangtubuh->gambar);
                if (file_exists($gambarPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Gambar');
                    $drawing->setDescription('Penjelasan');
                    $drawing->setPath($gambarPath);
                    $drawing->setHeight(90);
                    $drawing->setCoordinates('C' . $rowCounter);
                    $drawing->setOffsetX(20);
                    $drawing->setOffsetY(10);
                    $drawings[] = $drawing;
                }
            }
            $rowCounter += $group->count();
        }

        return $drawings;
    }
}
