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
    private $from;
    private $to;
    private $rowNumber = 0;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function properties(): array
    {
        return [
            'creator' => 'Equitry',
            'lastModifiedBy' => 'Equitry',
            'title' => 'Data Absensi',
            'description' => 'Data Absensi',
            'subject' => 'Data Absensi',
            'keywords' => 'absen',
            'category' => 'absen',
            'manager' => 'Equitry',
        ];
    }

    public function query()
    {
        $query = Attendance::query();
        $user = Auth::user();
        if ($user->roles->pluck('name')[0] == 'teacher') {
            $query->whereHas('schedule.subject', function ($query) use ($user) {
                $query->where('teacher_id', $user->teacher->id);
            })->with('schedule.subject');
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
            $attendance->student->grade->name . ' ' . $attendance->student->major->acronym . ' ' . $attendance->student->group->number,
            $attendance->status,
            $attendance->schedule->subject->name ?? '-',
            $attendance->created_at,
        ];
    }
    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Kelas',
            'Status',
            'Mapel',
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
        $sheet->getStyle('A2:F' . $this->rowNumber + 1)->applyFromArray($styleArray);
        $sheet->getStyle('A1:F1')->applyFromArray([
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
