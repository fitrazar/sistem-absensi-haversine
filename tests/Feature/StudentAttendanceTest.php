<?php

use Carbon\Carbon;
use App\Models\Time;
use App\Models\User;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Major;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->role = Role::create([
        'name' => 'student',
    ]);
    $this->role2 = Role::create([
        'name' => 'teacher',
    ]);

    $this->user = User::create([
        'username' => 'student123',
        'password' => bcrypt('password'),
    ]);
    $this->user->assignRole($this->role->name);

    $this->user2 = User::create([
        'username' => 'teacher123',
        'password' => bcrypt('password'),
    ]);
    $this->user2->assignRole($this->role2->name);

    $this->grade = Grade::create([
        'name' => 'X',
        'status' => 1,
    ]);
    $this->major = Major::create([
        'acronym' => 'TKJ',
        'name' => 'Teknik Komputer Jaringan',
        'status' => 1,
    ]);
    $this->group = Group::create([
        'number' => 1,
        'status' => 1,
    ]);

    $this->student = Student::create([
        'user_id' => $this->user->id,
        'grade_id' => $this->grade->id,
        'major_id' => $this->major->id,
        'group_id' => $this->group->id,
        'nisn' => '1234567890',
        'name' => 'John Doe',
        'gender' => 'Laki - Laki',
        'phone' => '08123456789',
        'address' => 'Jl. Merdeka',
        'photo' => null,
        'point' => 100,
    ]);

    $this->teacher = Teacher::create([
        'user_id' => $this->user2->id,
        'name' => 'Firman Syahrani',
        'nip' => 1000,
        'gender' => 'Laki - Laki',
        'phone' => '6281385931773',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Setting::create([
        'name' => 'Nama Sekolah',
        'logo' => 'logo.png',
        'coordinate' => '-6.200000,106.816666',
    ]);

    Time::create([
        'time_in_early' => '21:00:00',
        'time_in_lately' => '22:30:00',
        'time_out_early' => '23:00:00',
        'time_out_lately' => '00:00:00',
    ]);
});

test('student can successfully perform absen masuk', function () {
    Carbon::setTestNow('21:30:00');

    $response = $this->actingAs($this->user)->post('/siswa/attendance', [
        'id' => $this->student->id,
        'latitude' => '-6.200000',
        'longitude' => '106.816666',
    ]);

    $response->assertJson(['message' => 'Absen Masuk Berhasil!']);

    $this->assertDatabaseHas('attendances', [
        'student_id' => $this->student->id,
        'status' => 'Absen Masuk',
        'coordinate' => '-6.200000,106.816666',
    ]);
});

test('student cannot perform absen outside allowed radius', function () {
    Carbon::setTestNow('21:30:00');

    $response = $this->actingAs($this->user)->post('/siswa/attendance', [
        'id' => $this->student->id,
        'latitude' => '-7.250445',
        'longitude' => '112.768845',
    ]);

    $response->assertJson(['message' => 'Anda berada di luar radius absen yang diizinkan']);
    $this->assertDatabaseMissing('attendances', [
        'student_id' => $this->student->id,
    ]);
});

test('student cannot perform absen sakit without uploading file', function () {
    Carbon::setTestNow('21:30:00');

    $response = $this->actingAs($this->user)->post('/siswa/attendance', [
        'id' => $this->student->id,
        'status' => 'Sakit',
        'note' => 'Sakit',
    ]);

    $response->assertSessionHasErrors('file');
});


test('student can successfully perform absen masuk (terlambat)', function () {

    Carbon::setTestNow('22:31:00');

    $late = round(Carbon::now()->diffInMinutes('22:30:00'));

    $response = $this->actingAs($this->user)->post('/siswa/attendance', [
        'id' => $this->student->id,
        'latitude' => '-6.200000',
        'longitude' => '106.816666',
    ]);

    $response->assertJson(['message' => 'Absen Berhasil, Status Terlambat!']);

    $this->assertDatabaseHas('attendances', [
        'student_id' => $this->student->id,
        'status' => 'Absen Masuk (Terlambat ' . $late . ' menit)',
        'coordinate' => '-6.200000,106.816666',
    ]);
});

test('student cannot perform absen pulang before allowed time', function () {
    Carbon::setTestNow('01:00:00');

    $response = $this->actingAs($this->user)->post('/siswa/attendance', [
        'id' => $this->student->id,
        'latitude' => '-6.200000',
        'longitude' => '106.816666',
    ]);

    $response->assertJson(['message' => 'Waktu absen telah habis!']);
});

test('student can perform absen mapel if already performed absen masuk', function () {

    Attendance::create([
        'student_id' => $this->student->id,
        'status' => 'Absen Masuk',
        'coordinate' => '-6.200000,106.816666',
        'created_at' => now(),
    ]);

    Schedule::create([
        'day' => 'Rabu',
        'time_in' => '08:00:00',
        'time_out' => '10:00:00',
        'grade_id' => $this->grade->id,
        'major_id' => $this->major->id,
        'group_id' => $this->group->id,
        'subject_id' => Subject::create(['name' => 'Matematika', 'teacher_id' => $this->teacher->id])->id,
    ]);

    Carbon::setTestNow('09:00:00');

    $response = $this->actingAs($this->user)->post('/siswa/attendance', [
        'id' => $this->student->id,
        'status' => 'Absen Mapel',
        'latitude' => '-6.200000',
        'longitude' => '106.816666',
    ]);

    $response->assertJson(['message' => 'Absen Mapel Berhasil Dilakukan!']);

    $this->assertDatabaseHas('attendances', [
        'student_id' => $this->student->id,
        'status' => 'Absen Mapel',
        'coordinate' => '-6.200000,106.816666',
    ]);
});



// test('student can perform absen sakit with valid file upload', function () {
//     Storage::fake('local');

//     $response = $this->actingAs($this->user)->post('/siswa/attendance', [
//         'id' => $this->student->id,
//         'status' => 'Sakit',
//         'file' => UploadedFile::fake()->image('surat_sakit.jpg'),
//     ]);

//     $response->assertJson(['message' => 'Absen Sakit Berhasil Dilakukan']);

//     Storage::disk('local')->assertExists('attendance/John Doe/' . now()->timestamp . '.png');

//     $this->assertDatabaseHas('attendances', [
//         'student_id' => $this->student->id,
//         'status' => 'Sakit',
//     ]);
// });
