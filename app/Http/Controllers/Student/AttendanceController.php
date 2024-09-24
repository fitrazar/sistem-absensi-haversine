<?php

namespace App\Http\Controllers\Student;

use Carbon\Carbon;
use App\Models\Time;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('student.attendance.index');
    }

    public function store(Request $request)
    {

        $student = NULL;
        $userId = $request->id;
        $stats = $request->status ?? NULL;
        $note = $request->izin ?? NULL;
        $today = Carbon::today();
        $currentTime = Carbon::now();

        $student = Student::findOrFail($userId);
        $coord = Setting::first();
        $parts = explode(',', $coord->coordinate);
        $centerLat = (float) trim($parts[0]);
        $centerLng = (float) trim($parts[1]);
        $radius = 0.5;

        $absenLat = $request->latitude ?? NULL;
        $absenLng = $request->longitude ?? NULL;

        $distance = $this->haversine($centerLat, $centerLng, $absenLat, $absenLng);



        $waktuAbsen = Time::first();

        if (!$waktuAbsen) {
            return response()->json(['message' => 'Waktu absen tidak ditemukan']);
        }

        $timeInEarly = Carbon::createFromFormat('H:i:s', $waktuAbsen->time_in_early);
        $timeInLate = Carbon::createFromFormat('H:i:s', $waktuAbsen->time_in_lately);
        $timeOutEarly = Carbon::createFromFormat('H:i:s', $waktuAbsen->time_out_early);
        $timeOutLate = Carbon::createFromFormat('H:i:s', $waktuAbsen->time_out_lately);

        $status = '';
        $pointPenalty = 0;
        $message = '';


        $existingAttendanceIzin = Attendance::where('student_id', $student->id)
            ->whereDate('created_at', $today)
            ->where('status', 'Izin')
            ->first();
        $existingAttendanceSakit = Attendance::where('student_id', $student->id)
            ->whereDate('created_at', $today)
            ->where('status', 'Sakit')
            ->first();
        $existingAttendanceMasuk = Attendance::where('student_id', $student->id)
            ->whereDate('created_at', $today)
            ->where('status', 'Absen Masuk')
            ->first();
        $existingAttendanceTerlambat = Attendance::where('student_id', $student->id)
            ->whereDate('created_at', $today)
            ->where('status', 'like', '%terlambat%')
            ->first();
        $existingAttendancePulang = Attendance::where('student_id', $student->id)
            ->whereDate('created_at', $today)
            ->where('status', 'Absen Pulang')
            ->first();

        if ($request->status == 'Absen Mapel') {
            if (!$existingAttendanceMasuk && !$existingAttendanceTerlambat) {
                return response()->json(['message' => 'Anda harus melakukan absen masuk terlebih dahulu']);
            }

            $currentDay = now()->dayOfWeek;
            $days = [
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
            ];
            if ($currentDay == 0 || $currentDay == 6) {
                return response()->json(['message' => 'Absen Mapel Tidak Bisa Dilakukan Pada Hari Libur']);
            }
            $currentTime = now()->format('H:i:s');

            $schedule = Schedule::where('day', $days[$currentDay])
                ->where('time_in', '<=', $currentTime)
                ->where('time_out', '>=', $currentTime)
                ->where('grade_id', $student->grade_id)
                ->where('major_id', $student->major_id)
                ->where('group_id', $student->group_id)
                ->first();

            if (!$schedule) {
                return response()->json(['message' => 'Absen Mapel Tidak Bisa Dilakukan, Tidak Ada Jadwal Mapel Saat Ini']);
            }

            $existingAttendanceMapel = Attendance::where('student_id', $student->id)
                ->whereDate('created_at', $today)
                ->where('schedule_id', $schedule->id)
                ->first();

            if ($existingAttendanceMapel) {
                return response()->json(['message' => 'Anda sudah melakukan absen di mapel ' . $schedule->subject->name . ' hari ini']);
            }

            Attendance::create([
                'student_id' => $student->id,
                'schedule_id' => $schedule->id,
                'coordinate' => $absenLat . ',' . $absenLng,
                'status' => 'Absen Mapel',
                'note' => $note
            ]);

            return response()->json(['message' => 'Absen Mapel Berhasil Dilakukan!']);
        }

        if ($currentTime->between($timeInEarly, $timeInLate)) {
            if ($existingAttendanceMasuk || $existingAttendanceIzin || $existingAttendanceSakit) {
                return response()->json(['message' => 'Anda sudah melakukan absen masuk hari ini']);
            }
            if ($stats) {
                $status = $stats;
            } else {
                $status = 'Absen Masuk';
            }
            $message = 'Absen Masuk Berhasil!';
        } elseif ($currentTime->gt($timeInLate) && $currentTime->lt($timeOutEarly)) {
            if ($existingAttendanceMasuk || $existingAttendanceTerlambat || $existingAttendanceIzin || $existingAttendanceSakit) {
                return response()->json(['message' => 'Anda sudah melakukan absen masuk hari ini']);
            }
            if ($stats) {
                $status = $stats;
            } else {
                $late = round($currentTime->diffInMinutes($timeInLate));
                $status = 'Absen Masuk (Terlambat ' . $late . ' menit)';
                $pointPenalty = 2;
            }
            $message = 'Absen Berhasil, Status Terlambat!';
        } elseif ($currentTime->between($timeOutEarly, $timeOutLate)) {
            if ($existingAttendancePulang || $existingAttendanceIzin || $existingAttendanceSakit) {
                return response()->json(['message' => 'Anda sudah melakukan absen pulang hari ini']);
            }
            if ($stats) {
                $status = $stats;
            } else {
                $status = 'Absen Pulang';
            }
            $message = 'Absen Pulang Berhasil!';
        } else if ($currentTime->gt($timeOutLate) && $currentTime->lt($timeInEarly)) {
            return response()->json(['message' => 'Waktu absen telah habis!']);
        }

        if (!$absenLat && !$absenLng || $stats) {
            if ($stats == 'Sakit') {
                $request->validate([
                    'file' => 'required|image|mimes:jpg,jpeg,png|max:4096',
                ], [
                    'file.required' => 'Surat Sakit wajib diisi!',
                    'file.image' => 'Surat Sakit harus berupa gambar!',
                    'file.mimes' => 'Surat Sakit harus format jpg,jpeg,png!',
                    'file.max' => 'Surat Sakit tidak boleh melebihi 4MB!',
                ]);
                $filename = time() . '.png';
                $path = $request->file('file')->storeAs('attendance/' . $student->name . '/', $filename);

                Attendance::create([
                    'student_id' => $student->id,
                    'coordinate' => NULL,
                    'status' => $status,
                    'note' => $filename
                ]);

                return response()->json(['message' => 'Absen Sakit Berhasil Dilakukan']);

            } else {
                $request->validate([
                    'note' => 'required|string|max:100',
                ], [
                    'note.required' => 'Keterangan wajib diisi!',
                    'note.string' => 'Keterangan harus berupa teks!',
                    'note.max' => 'Keterangan tidak boleh lebih dari 100 karakter!',
                ]);
                Attendance::create([
                    'student_id' => $student->id,
                    'coordinate' => NULL,
                    'status' => $status,
                    'note' => $note
                ]);

                return response()->json(['message' => 'Absen Izin Berhasil Dilakukan!']);

            }
        }


        if ($distance <= $radius) {
            Attendance::create(attributes: [
                'student_id' => $student->id,
                'coordinate' => $absenLat . ',' . $absenLng,
                'status' => $status,
                'note' => NULL
            ]);

            if ($student && $pointPenalty > 0) {
                $student->point -= $pointPenalty;
                $student->save();
            }

            return response()->json(['message' => $message]);
        } else {
            return response()->json(['message' => 'Anda berada di luar radius absen yang diizinkan']);
        }

    }


    private function haversine($centerLat, $centerLng, $absenLat, $absenLng)
    {
        $earthRadius = 6371;

        $deltaLat = deg2rad($absenLat - $centerLat);
        $deltaLng = deg2rad($absenLng - $centerLng);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos(deg2rad($centerLat)) * cos(deg2rad($absenLat)) *
            sin($deltaLng / 2) * sin($deltaLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }
}
