<?php

use App\Models\User;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Major;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    // Simulasi user dan student

    $this->role = Role::create([
        'name' => 'student',
    ]);

    $this->user = User::create([
        'username' => 'student123',
        'password' => bcrypt('password'),
    ]);
    $this->user->assignRole($this->role->name);


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
});

test('profile can be updated with valid data', function () {

    $response = $this
        ->actingAs($this->user)
        ->post('/profile', [
            'phone' => '08123456780',
            'address' => 'Jl. Kebangsaan',
        ]);

    $response->assertRedirect('/profile');

    $this->assertDatabaseHas('students', [
        'user_id' => $this->user->id,
        'phone' => '08123456780',
        'address' => 'Jl. Kebangsaan',
    ]);
});


test('phone must be numeric', function () {
    $response = $this
        ->actingAs($this->user)
        ->post('/profile', [
            'phone' => 'not-a-number',
        ]);

    $response->assertSessionHasErrors('phone');
});

test('address can be nullable', function () {

    $response = $this
        ->actingAs($this->user)
        ->post('/profile', [
            'address' => null,
        ]);

    $response->assertRedirect('/profile');

    $this->assertDatabaseHas('students', [
        'user_id' => $this->user->id,
        'address' => null,
    ]);
});

test('photo must be an image and within size limit', function () {

    Storage::fake('local');

    $response = $this
        ->actingAs($this->user)
        ->post('/profile', [
            'photo' => UploadedFile::fake()->create('document.pdf', 500),
        ]);

    $response->assertSessionHasErrors('photo');

    $response = $this
        ->actingAs($this->user)
        ->post('/profile', [
            'photo' => UploadedFile::fake()->image('large-photo.jpg')->size(5000),
        ]);

    $response->assertSessionHasErrors('photo');
});

// test('photo can be uploaded and stored', function () {

//     Storage::fake('public');

//     $photo = UploadedFile::fake()->image('photo.jpg', 500, 500);

//     $response = $this
//         ->actingAs($this->user)
//         ->post('/profile', [
//             'photo' => $photo,
//         ]);

//     // Assert response berhasil
//     $response->assertRedirect('/profile');

//     Storage::disk('public')->assertExists('student/photo/' . $photo->hashName());

//     $this->assertDatabaseHas('students', [
//         'user_id' => $this->user->id,
//         'photo' => $photo->hashName(),
//     ]);
// });

