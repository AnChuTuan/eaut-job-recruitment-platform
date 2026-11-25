<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'job_id',
        'candidate_id',
        'cv_id',
        'cv_url',
        'cover_letter',
        'status',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
