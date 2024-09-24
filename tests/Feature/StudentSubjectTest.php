<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Major;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Schedule;
use Spatie\Permission\Models\Role;

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
    $this->grade2 = Grade::create([
        'name' => 'XI',
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
});

test('student can view today’s schedule based on grade, major, and group', function () {

    Carbon::setTestNow(Carbon::createFromDate(2024, 9, 23));

    $schedule = Schedule::create([
        'day' => 'Senin',
        'time_in' => '08:00:00',
        'time_out' => '10:00:00',
        'grade_id' => $this->student->grade->id,
        'major_id' => $this->student->major->id,
        'group_id' => $this->student->group->id,
        'subject_id' => Subject::create(['name' => 'Matematika', 'teacher_id' => $this->teacher->id])->id,
    ]);

    $response = $this->actingAs($this->user)->get('/siswa/subject');

    $response->assertStatus(200);
    $response->assertViewIs('student.subject.index');
    $response->assertViewHas('schedules', function ($schedules) use ($schedule) {
        return $schedules->contains($schedule);
    });
});

test('student can search for a specific subject in today’s schedule', function () {
    Carbon::setTestNow(Carbon::createFromDate(2024, 9, 23));

    $mathSchedule = Schedule::create([
        'day' => 'Senin',
        'time_in' => '08:00:00',
        'time_out' => '10:00:00',
        'grade_id' => $this->student->grade->id,
        'major_id' => $this->student->major->id,
        'group_id' => $this->student->group->id,
        'subject_id' => Subject::create(['name' => 'Matematika', 'teacher_id' => $this->teacher->id])->id,
    ]);

    $englishSchedule = Schedule::create([
        'day' => 'Senin',
        'time_in' => '10:00:00',
        'time_out' => '12:00:00',
        'grade_id' => $this->student->grade->id,
        'major_id' => $this->student->major->id,
        'group_id' => $this->student->group->id,
        'subject_id' => Subject::create(['name' => 'Bahasa Inggris', 'teacher_id' => $this->teacher->id])->id,
    ]);

    $response = $this->actingAs($this->user)->get('/siswa/subject?search=Matematika');

    $response->assertStatus(200);
    $response->assertViewIs('student.subject.index');
    $response->assertViewHas('schedules', function ($schedules) use ($mathSchedule, $englishSchedule) {
        return $schedules->contains($mathSchedule) && !$schedules->contains($englishSchedule);
    });
});

test('student sees no schedule if not matching grade, major, or group', function () {
    Carbon::setTestNow(Carbon::createFromDate(2024, 9, 23));

    $schedule = Schedule::create([
        'day' => 'Senin',
        'time_in' => '08:00:00',
        'time_out' => '10:00:00',
        'grade_id' => $this->grade2->id,
        'major_id' => $this->student->major->id,
        'group_id' => $this->student->group->id,
        'subject_id' => Subject::create(['name' => 'Matematika', 'teacher_id' => $this->teacher->id])->id,
    ]);

    $response = $this->actingAs($this->user)->get('/siswa/subject');

    $response->assertStatus(200);
    $response->assertViewIs('student.subject.index');
    $response->assertViewHas('schedules', function ($schedules) {
        return $schedules->isEmpty();
    });
});
