<?php

use Workbench\App\Models;

it('migrations runs succesfully', function () {

    $school = Models\School::factory()->create();
    $student = Models\Student::factory()->create();
    $level = Models\Level::factory()->create();
    $course = Models\Course::factory()->create();
    $gender = Models\Gender::factory()->create();
    $session = Models\SchoolSession::factory()->create();
    $score = Models\Score::factory()->create();
    $semester = Models\Semester::factory()->create();
    //$user = Models\User::factory()->create();
    $year = Models\Year::factory()->create();
    $subject = Models\Subject::factory()->create();
    $teacher = Models\Teacher::factory()->create();

    $this->assertModelExists($school);
    $this->assertModelExists($teacher);
    $this->assertModelExists($subject);
    $this->assertModelExists($year);
    //$this->assertModelExists($user);
    $this->assertModelExists($semester);
    $this->assertModelExists($score);
    $this->assertModelExists($session);
    $this->assertModelExists($gender);
    $this->assertModelExists($level);
    $this->assertModelExists($course);
    $this->assertModelExists($student);
    $this->assertModelExists($school);

});
