<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     **/
    public function up(): void
    {
        Schema::create('school_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('school_id')->constrained();
            $table->foreignId('year_id')->constrained();
            $table->foreignId('semester_id')->constrained();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('school_teacher', function (Blueprint $table) {
            $table->id();
            //$table->foreignId('session_id')->constrained('school_sessions');
            $table->foreignId('teacher_id')->constrained();
            $table->foreignId('school_id')->constrained();
            $table->timestamps();
        });

        Schema::create('school_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('school_sessions');
            $table->foreignId('student_id')->constrained();
            $table->foreignId('school_id')->constrained();

            $table->timestamp('enrolled_at');
            $table->foreignId('enrolled_by')->nullable();
            $table->boolean('active')->default(true);
            $table->foreignId('guardian_id')->nullable();
            $table->timestamps();
        });

        Schema::create('level_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('school_sessions');
            $table->foreignId('level_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('session_id')->constrained('school_sessions')->restrictOnDelete();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->foreignId('level_id')->constrained()->restrictOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('coefficient');
            $table->string('code')->nullable();
            $table->timestamps();
        });
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->unsignedSmallInteger('marks');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_sessions');
        Schema::dropIfExists('school_teacher');
        Schema::dropIfExists('school_student');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('scores');
    }
};
