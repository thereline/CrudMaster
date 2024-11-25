<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'active', 'school_id',
    ];

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function level(): BelongsToMany
    {
        return $this->belongsToMany(Level::class);
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(
            School::class,
            'school_student',
            'student_id',
            'school_id')
            ->as('registrations')
            ->withPivot('enrolled_at', 'enrolled_by', 'active')
            ->withTimestamps();
    }

    public function schoolSessions(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolSession::class,
            'school_student',
            'student_id',
            'session_id')
            ->as('registrations')
            ->withPivot('school_id', 'registered_on', 'registered_by', 'active')
            ->withTimestamps();
    }
}
