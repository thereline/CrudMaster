<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['name', 'email', 'active'];

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'school_student')
            ->as('registrations')
            ->withPivot('enrolled_at', 'enrolled_by', 'active')
            ->withTimestamps();
    }

    public function principal(): HasOne
    {
        return $this->hasOne(Principal::class);
    }
}
