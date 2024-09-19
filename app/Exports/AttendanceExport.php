<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, WithStyles, ShouldAutoSize, WithProperties
{
    use Exportable;
    private $place;
    private $from;
    private $to;
    private $rowNumber = 0;

    public function __construct($place, $from, $to)
    {
        $this->place = $place;
        $this->from = $from;
        $this->to = $to;
    }

    public function properties(): array
    {
        return [
            'creator' => 'Fitra Fajar',
            'lastModifiedBy' => 'Fitra Fajar',
            'title' => 'Data Absensi',
            'description' => 'Data Absensi',
            'subject' => 'Data Absensi',
            'keywords' => 'absen',
            'category' => 'absen',
            'manager' => 'Fitra Fajar',
        ];
    }

    public function query()
    {
        $query = Attendance::query();
        $user = Auth::user();
        if ($user->roles->pluck('name')[0] == 'admin') {
            if ($this->place != '' && $this->place != 'All') {
                $query->whereHas('student.internship.recommendation', function ($query) {
                    $query->where('id', $this->place);
                })->with('student.internship.recommendation');
            }
        } else if ($user->roles->pluck('name')[0] == 'teacher') {
            if ($this->place != '' && $this->place != 'All') {
                $query->whereHas('student.internship.recommendation', function ($query) use ($user) {
                    $query->where('teacher_id', $user->teacher->id)->where('id', $this->place);
                })->with('student.internship.recommendation');
            } else {
                $query->whereHas('student.internship.recommendation', function ($query) use ($user) {
                    $query->where('teacher_id', $user->teacher->id);
                })->with('student.internship.recommendation');
            }
        } else {
            if ($this->place != '' && $this->place != 'All') {
                $query->whereHas('student.internship.recommendation', function ($query) use ($user) {
                    $query->where('major_id', $user->headmaster->major_id)->where('id', $this->place);
                })->with('student.internship.recommendation');
            } else {
                $query->whereHas('student.internship.recommendation', function ($query) use ($user) {
                    $query->where('major_id', $user->headmaster->major_id);
                })->with('student.internship.recommendation');
            }
        }

        if ($this->from && $this->to) {
            $from1 = Carbon::parse($this->from)->startOfDay();
            $to1 = Carbon::parse($this->to)->endOfDay();
            $query->whereBetween('created_at', [$from1, $to1]);
        }

        return $query;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function map($attendance): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $attendance->student->name,
            $attendance->student->internship->recommendation->name,
            $attendance->status,
            $attendance->created_at,
        ];
    }
    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Tempat PKL',
            'Status',
            'Tanggal',
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
