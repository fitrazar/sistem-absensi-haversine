<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StudentExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, WithStyles, ShouldAutoSize, WithProperties
{
    use Exportable;
    private $major;
    private $grade;
    private $rowNumber = 0;

    public function __construct($major, $grade)
    {
        $this->major = $major;
        $this->grade = $grade;
    }

    public function properties(): array
    {
        return [
            'creator' => 'Equitry',
            'lastModifiedBy' => 'Equitry',
            'title' => 'Data Siswa',
            'description' => 'Data Siswa',
            'subject' => 'Data Siswa',
            'keywords' => 'siswa',
            'category' => 'siswa',
            'manager' => 'Equitry',
        ];
    }

    public function query()
    {
        $query = Student::with(['grade', 'major', 'group'])->select('students.*');

        if ($this->major != '' && $this->major != 'All') {
            $query->whereHas('major', function ($q) {
                $q->where('id', $this->major);
            });
        }
        if ($this->grade != '' && $this->grade != 'All') {
            $query->whereHas('grade', function ($q) {
                $q->where('name', $this->grade);
            });
        }

        $query->orderBy('name');

        return $query;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function map($student): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $student->nisn,
            $student->name,
            $student->gender,
            $student->grade->name . ' ' . $student->major->acronym . ' ' . $student->group->number,
        ];
    }
    public function headings(): array
    {
        return [
            'No',
            'NISN',
            'Nama',
            'JK',
            'Rombel',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $count = Student::count();
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
        $sheet->getStyle('A2:E' . $this->rowNumber + 1)->applyFromArray($styleArray);
        $sheet->getStyle('A1:E1')->applyFromArray([
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
