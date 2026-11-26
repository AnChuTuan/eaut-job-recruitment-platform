<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    protected $fillable = [
        'company_id',
        'title',
        'category',
        'job_level',
        'employment_type',
        'quantity',
        'deadline',
        'salary_min',
        'salary_max',
        'currency',
        'location',
        'description',
        'requirements',
        'benefits',
        'skills',
        'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
