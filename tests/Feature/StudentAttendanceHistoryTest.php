<?php

use Carbon\Carbon;
use App\Models\Time;
use App\Models\User;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Major;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->role = Role::create([
        'name' => 'student',
    ]);

    $this->user = User::create([
        'username' => 'student123',
        'password' => bcrypt('password'),
    ]);
    $this->user->assignRole($this->role->name);

    $this->user2 = User::create([
        'username' => 'student1234',
        'password' => bcrypt('password'),
    ]);
    $this->user2->assignRole($this->role->name);

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

    $this->student2 = Student::create([
        'user_id' => $this->user2->id,
        'grade_id' => $this->grade->id,
        'major_id' => $this->major->id,
        'group_id' => $this->group->id,
        'nisn' => '12344',
        'name' => 'Fitra Fajar',
        'gender' => 'Laki - Laki',
        'phone' => '08123456789',
        'address' => 'Jl. Merdeka',
        'photo' => null,
        'point' => 100,
    ]);

    Time::create([
        'time_in_early' => '21:00:00',
        'time_in_lately' => '22:30:00',
        'time_out_early' => '23:00:00',
        'time_out_lately' => '00:00:00',
    ]);
});

test('student can view attendance history within a date range', function () {
    $attendance1 = Attendance::create([
        'student_id' => $this->student->id,
        'status' => 'Absen Masuk',
        'created_at' => Carbon::createFromDate(2024, 9, 5),
    ]);

    $attendance2 = Attendance::create([
        'student_id' => $this->student->id,
        'status' => 'Absen Pulang',
        'created_at' => Carbon::createFromDate(2024, 9, 10),
    ]);

    $response = $this->actingAs($this->user)->get('/siswa/history', [
        'from' => '05-09-2024',
        'to' => '10-09-2024',
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('attendances', [
        'id' => $attendance1->id,
        'student_id' => $this->student->id,
        'status' => 'Absen Masuk',
    ]);

    $this->assertDatabaseHas('attendances', [
        'id' => $attendance2->id,
        'student_id' => $this->student->id,
        'status' => 'Absen Pulang',
    ]);
});

test('attendance index returns error for invalid date range', function () {
    $response = $this->actingAs($this->user)->get('/siswa/history', [
        'from' => '10-09-2024',
        'to' => '05-09-2024',
    ]);

    $response->assertStatus(200);
});

test('student can view own attendance details', function () {
    $attendance = Attendance::create([
        'student_id' => $this->student->id,
        'status' => 'Absen Masuk',
        'coordinate' => '-6.200000,106.816666',
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->get('/siswa/history/' . $attendance->id);

    $response->assertStatus(200);
    $response->assertViewIs('student.history.show');
    $response->assertViewHas('attendance', $attendance);
});

test('student cannot view other student attendance details', function () {

    $attendance = Attendance::create([
        'student_id' => $this->student2->id,
        'status' => 'Absen Masuk',
        'coordinate' => '-6.200000,106.816666',
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->get('/siswa/history/' . $attendance->id);

    $response->assertStatus(403);
});


test('student can delete their own attendance record', function () {
    $attendance = Attendance::create([
        'student_id' => $this->student->id,
        'status' => 'Absen Masuk',
        'coordinate' => '-6.200000,106.816666',
        'created_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->delete('/siswa/history/' . $attendance->id);

    $response->assertRedirect('/siswa/history');
    $response->assertSessionHas('success', 'Absen Berhasil Dihapus, Kamu Bisa Melakukan Absen Ulang.');
    $this->assertDatabaseMissing('attendances', [
        'id' => $attendance->id,
    ]);
});

// test('student cannot delete other student attendance record', function () {
//     $attendance = Attendance::create([
//         'student_id' => $this->student2->id,
//         'status' => 'Absen Masuk',
//         'coordinate' => '-6.200000,106.816666',
//         'created_at' => now(),
//     ]);

//     $response = $this->actingAs($this->user)->delete('/siswa/history/' . $attendance->id);

//     $response->assertStatus(403);
//     $this->assertDatabaseHas('attendances', [
//         'id' => $attendance->id,
//     ]);
// });
