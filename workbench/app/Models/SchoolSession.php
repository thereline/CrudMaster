<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolSession extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolSessionFactory> */
    use HasFactory;

    protected $fillable = ['parent_id', 'school_id', 'name', 'semester_id', 'year_id'];

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'session_id');
    }
}
