<?php

namespace App\Exports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubjectExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, WithStyles, ShouldAutoSize, WithProperties
{
    use Exportable;
    private $rowNumber = 0;
    public function properties(): array
    {
        return [
            'creator' => 'Equitry',
            'lastModifiedBy' => 'Equitry',
            'title' => 'Data Mapel',
            'description' => 'Data Mapel',
            'subject' => 'Data Mapel',
            'keywords' => 'mapel',
            'category' => 'mapel',
            'manager' => 'Equitry',
        ];
    }

    public function query()
    {
        $query = Subject::query();

        $query->orderBy('name');

        return $query;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function map($subject): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $subject->teacher->name,
            $subject->name,
            $subject->description,
        ];
    }
    public function headings(): array
    {
        return [
            'No',
            'Guru',
            'Nama Mapel',
            'Deskripsi',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'font' => [
                'size' => 12,
                'name' => 'Times New Roman'
            ],
        ];
        $sheet->getStyle('A2:D' . $this->rowNumber + 1)->applyFromArray($styleArray);
        $sheet->getStyle('A1:D1')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'font' => [
                'bold' => true,
                'size' => 13,
                'name' => 'Times New Roman'
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
    }
}
