<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Principal extends Model
{
    /** @use HasFactory<\Database\Factories\PrincipalFactory> */
    use HasFactory;

    protected $fillable = ['school_id', 'name', 'email'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class);
    }
}
